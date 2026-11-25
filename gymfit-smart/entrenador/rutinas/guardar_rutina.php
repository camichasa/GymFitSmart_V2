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

$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$alumno_id = $_POST["alumno_id"];
$dia = $_POST["dia"];
$entrenador_id = $_SESSION["id_usuario"];

try {

    $base = new PDO(
        "mysql:host=localhost;dbname=gymfit_smart;charset=utf8mb4",
        "root",
        ""
    );

    $base->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $sql = "INSERT INTO rutinas
            (nombre, descripcion, alumno_id, entrenador_id, dia)
            VALUES
            (:nombre, :descripcion, :alumno_id, :entrenador_id, :dia)";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":nombre" => $nombre,
            ":descripcion" => $descripcion,
            ":alumno_id" => $alumno_id,
            ":entrenador_id" => $entrenador_id,
            ":dia" => $dia
        )
    );

    header("Location: listado_rutinas.php");
    exit();

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

} finally {

    $base = null;

}

?>