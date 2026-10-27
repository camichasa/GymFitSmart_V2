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
$rutina_id = $_POST["rutina_id"];
$series = $_POST["series"];
$repeticiones = $_POST["repeticiones"];
$peso = $_POST["peso"];
$orden = $_POST["orden"];
$notas = $_POST["notas"];

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

    $sql = "INSERT INTO ejercicios
            (rutina_id, nombre, series, repeticiones, peso_sugerido, orden, notas)
            VALUES
            (:rutina_id, :nombre, :series, :repeticiones, :peso, :orden, :notas)";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":rutina_id" => $rutina_id,
            ":nombre" => $nombre,
            ":series" => $series,
            ":repeticiones" => $repeticiones,
            ":peso" => $peso,
            ":orden" => $orden,
            ":notas" => ($notas === "" ? null : $notas)
        )
    );

    header("Location: listado_ejercicios.php");
    exit();

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

} finally {

    $base = null;

}

?>