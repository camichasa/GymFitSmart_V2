<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/iniciar_sesion.html");
    exit();
}

if ($_SESSION["rol"] != "alumno") {

    header("Location: ../entrenador/panel_entrenador.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: mis_datos.php");
    exit();
}

require '../config/conexion_bd.php';


/* PANTALLA DE MENSAJE (misma estructura que las pantallas de registro) */

function mostrarMensaje($titulo, $texto, $enlace, $textoEnlace)
{
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="../assets/css/estilos_autenticacion.css">

        <title><?php echo htmlspecialchars($titulo); ?> - GymFit Smart</title>
    </head>

    <body>

        <main class="contenedor">

            <section class="tarjeta mensaje">

                <h1 class="logo-texto">GYMFIT SMART</h1>

                <h2><?php echo htmlspecialchars($titulo); ?></h2>

                <p><?php echo htmlspecialchars($texto); ?></p>

                <a href="<?php echo $enlace; ?>" class="btn-link">
                    <?php echo htmlspecialchars($textoEnlace); ?>
                </a>

                <br><br>

                <a href="inicio_alumno.php">
                    Ir al inicio
                </a>

            </section>

        </main>

    </body>

    </html>
<?php
    exit();
}


/* DATOS DEL FORMULARIO */

$peso = filter_var($_POST["peso"] ?? "", FILTER_VALIDATE_FLOAT);
$altura = filter_var($_POST["altura"] ?? "", FILTER_VALIDATE_INT);
$edad = filter_var($_POST["edad"] ?? "", FILTER_VALIDATE_INT);
$sexo = $_POST["sexo"] ?? "";
$objetivo = $_POST["objetivo"] ?? "";
$experiencia = $_POST["experiencia"] ?? "";
$dias = $_POST["Dias_entrenamiento"] ?? "";
$nombreContacto = trim($_POST["Nombre"] ?? "");
$telefonoContacto = preg_replace("/\D/", "", $_POST["Numero"] ?? "");


/* VALIDACION (mismos limites que el formulario) */

$errores = array();

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
    $errores[] = "Selecciona el sexo.";
}

if (!in_array($objetivo, array("Bajar peso", "Ganar musculo", "Mejorar mi salud"), true)) {
    $errores[] = "Selecciona un objetivo.";
}

if ($experiencia !== "" && !in_array($experiencia, array("Ninguna", "Poca", "Media", "Mucha"), true)) {
    $errores[] = "La experiencia seleccionada no es válida.";
}

if ($dias !== "" && (!ctype_digit($dias) || $dias < 1 || $dias > 7)) {
    $errores[] = "Los días de entrenamiento deben ser de 1 a 7.";
}

if (mb_strlen($nombreContacto) > 100) {
    $errores[] = "El nombre del contacto de emergencia es demasiado largo.";
}

if (strlen($telefonoContacto) < 8 || strlen($telefonoContacto) > 15) {
    $errores[] = "El teléfono de emergencia debe tener entre 8 y 15 dígitos.";
}

if (count($errores) > 0) {

    mostrarMensaje(
        "No se pudieron guardar los datos",
        implode(" ", $errores),
        "mis_datos.php",
        "VOLVER A MIS DATOS"
    );
}


/* GUARDAR (un solo registro por alumno: si ya existe, se actualiza) */

try {

    $sql = "INSERT INTO datos_alumno
            (alumno_id, peso, altura, edad, sexo, objetivo,
             experiencia, dias_entrenamiento,
             contacto_emergencia_nombre, contacto_emergencia_telefono)
            VALUES
            (:alumno_id, :peso, :altura, :edad, :sexo, :objetivo,
             :experiencia, :dias,
             :nombre_contacto, :telefono_contacto)
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

    $resultado = $pdo->prepare($sql);

    $resultado->execute(
        array(
            ":alumno_id" => $_SESSION["id_usuario"],
            ":peso" => round($peso, 1),
            ":altura" => $altura,
            ":edad" => $edad,
            ":sexo" => $sexo,
            ":objetivo" => $objetivo,
            ":experiencia" => ($experiencia === "" ? null : $experiencia),
            ":dias" => ($dias === "" ? null : (int)$dias),
            ":nombre_contacto" => ($nombreContacto === "" ? null : $nombreContacto),
            ":telefono_contacto" => $telefonoContacto
        )
    );

    mostrarMensaje(
        "Datos guardados",
        "Tus datos se guardaron correctamente.",
        "mis_datos.php",
        "VOLVER A MIS DATOS"
    );
} catch (Exception $e) {

    mostrarMensaje(
        "No se pudieron guardar los datos",
        "Ocurrió un error al guardar: " . $e->getMessage(),
        "mis_datos.php",
        "VOLVER A MIS DATOS"
    );
}
