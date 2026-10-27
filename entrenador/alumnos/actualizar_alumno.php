<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../../auth/iniciar_sesion.html");
    exit();
}

if ($_SESSION["rol"] != "entrenador") {
    header("Location: ../../alumno/inicio_alumno.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: listado_alumnos.php");
    exit();
}

require '../../config/conexion_bd.php';

$idAlumno = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);
$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$peso = filter_var($_POST["peso"] ?? "", FILTER_VALIDATE_FLOAT);
$altura = filter_var($_POST["altura"] ?? "", FILTER_VALIDATE_INT);
$edad = filter_var($_POST["edad"] ?? "", FILTER_VALIDATE_INT);
$sexo = $_POST["sexo"] ?? "";
$objetivo = $_POST["objetivo"] ?? "";
$experiencia = $_POST["experiencia"] ?? "";
$dias = $_POST["dias_entrenamiento"] ?? "";
$contactoNombre = trim($_POST["contacto_nombre"] ?? "");
$contactoTelefono = preg_replace("/\D/", "", $_POST["contacto_telefono"] ?? "");

$errores = array();

if ($idAlumno === false || $idAlumno <= 0) {
    $errores[] = "Alumno no válido.";
}

if ($nombre === "" || mb_strlen($nombre) > 100) {
    $errores[] = "El nombre no es válido.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    $errores[] = "El email no es válido.";
}

if ($peso === false || $peso < 20 || $peso > 300) {
    $errores[] = "El peso debe estar entre 20 y 300 kg.";
}

if ($altura === false || $altura < 100 || $altura > 250) {
    $errores[] = "La altura debe estar entre 100 y 250 cm.";
}

if ($edad === false || $edad < 10 || $edad > 100) {
    $errores[] = "La edad debe estar entre 10 y 100 años.";
}

if (!in_array($sexo, array("masculino", "femenino", "otro"), true)) {
    $errores[] = "El sexo seleccionado no es válido.";
}

if (!in_array($objetivo, array("Bajar peso", "Ganar musculo", "Mejorar mi salud"), true)) {
    $errores[] = "El objetivo seleccionado no es válido.";
}

if ($experiencia !== "" && !in_array($experiencia, array("Ninguna", "Poca", "Media", "Mucha"), true)) {
    $errores[] = "La experiencia seleccionada no es válida.";
}

if ($dias !== "" && (!ctype_digit($dias) || $dias < 1 || $dias > 7)) {
    $errores[] = "Los días de entrenamiento deben ser de 1 a 7.";
}

if (mb_strlen($contactoNombre) > 100) {
    $errores[] = "El nombre del contacto de emergencia es demasiado largo.";
}

if (strlen($contactoTelefono) < 8 || strlen($contactoTelefono) > 15) {
    $errores[] = "El teléfono de emergencia debe tener entre 8 y 15 dígitos.";
}

if (count($errores) > 0) {
    echo "<h2>No se pudieron actualizar los datos</h2>";
    echo "<p>" . htmlspecialchars(implode(" ", $errores)) . "</p>";
    echo '<p><a href="editar_alumno.php?id=' . (int)$idAlumno . '">Volver</a></p>';
    exit();
}

try {
    $pdo->beginTransaction();

    $sqlAlumno = "SELECT id
                  FROM usuarios
                  WHERE id = :id
                  AND rol = 'alumno'";

    $resultadoAlumno = $pdo->prepare($sqlAlumno);
    $resultadoAlumno->execute(array(":id" => $idAlumno));

    if (!$resultadoAlumno->fetch(PDO::FETCH_ASSOC)) {
        $pdo->rollBack();
        header("Location: listado_alumnos.php");
        exit();
    }

    $sqlEmail = "SELECT id
                 FROM usuarios
                 WHERE email = :email
                 AND id <> :id";

    $resultadoEmail = $pdo->prepare($sqlEmail);
    $resultadoEmail->execute(
        array(
            ":email" => $email,
            ":id" => $idAlumno
        )
    );

    if ($resultadoEmail->fetch(PDO::FETCH_ASSOC)) {
        $pdo->rollBack();
        echo "<h2>No se pudieron actualizar los datos</h2>";
        echo "<p>El email ingresado ya está registrado por otro usuario.</p>";
        echo '<p><a href="editar_alumno.php?id=' . $idAlumno . '">Volver</a></p>';
        exit();
    }

    $sqlUsuario = "UPDATE usuarios
                   SET nombre = :nombre,
                       email = :email
                   WHERE id = :id
                   AND rol = 'alumno'";

    $resultadoUsuario = $pdo->prepare($sqlUsuario);
    $resultadoUsuario->execute(
        array(
            ":nombre" => $nombre,
            ":email" => $email,
            ":id" => $idAlumno
        )
    );

    $sqlDatos = "INSERT INTO datos_alumno
                 (alumno_id, peso, altura, edad, sexo, objetivo,
                  experiencia, dias_entrenamiento,
                  contacto_emergencia_nombre, contacto_emergencia_telefono)
                 VALUES
                 (:alumno_id, :peso, :altura, :edad, :sexo, :objetivo,
                  :experiencia, :dias,
                  :contacto_nombre, :contacto_telefono)
                 ON DUPLICATE KEY UPDATE
                    peso = VALUES(peso),
                    altura = VALUES(altura),
                    edad = VALUES(edad),
                    sexo = VALUES(sexo),
                    objetivo = VALUES(objetivo),
                    experiencia = VALUES(experiencia),
                    dias_entrenamiento = VALUES(dias_entrenamiento),
                    contacto_emergencia_nombre = VALUES(contacto_emergencia_nombre),
                    contacto_emergencia_telefono = VALUES(contacto_emergencia_telefono)";

    $resultadoDatos = $pdo->prepare($sqlDatos);
    $resultadoDatos->execute(
        array(
            ":alumno_id" => $idAlumno,
            ":peso" => round($peso, 1),
            ":altura" => $altura,
            ":edad" => $edad,
            ":sexo" => $sexo,
            ":objetivo" => $objetivo,
            ":experiencia" => ($experiencia === "" ? null : $experiencia),
            ":dias" => ($dias === "" ? null : (int)$dias),
            ":contacto_nombre" => ($contactoNombre === "" ? null : $contactoNombre),
            ":contacto_telefono" => $contactoTelefono
        )
    );

    $pdo->commit();

    header("Location: ver_alumno.php?id=" . $idAlumno);
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "<h2>No se pudieron actualizar los datos</h2>";
    echo "<p>Ocurrió un error al guardar los cambios.</p>";
    echo '<p><a href="editar_alumno.php?id=' . (int)$idAlumno . '">Volver</a></p>';
}

?>
