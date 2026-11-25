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

$id = $_POST["id"];
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

    $sql = "UPDATE ejercicios
            INNER JOIN rutinas
            ON ejercicios.rutina_id = rutinas.id
            SET ejercicios.nombre = :nombre,
                ejercicios.rutina_id = :rutina_id,
                ejercicios.series = :series,
                ejercicios.repeticiones = :repeticiones,
                ejercicios.peso_sugerido = :peso,
                ejercicios.orden = :orden,
                ejercicios.notas = :notas
            WHERE ejercicios.id = :id
            AND rutinas.entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":nombre" => $nombre,
            ":rutina_id" => $rutina_id,
            ":series" => $series,
            ":repeticiones" => $repeticiones,
            ":peso" => $peso,
            ":orden" => $orden,
            ":notas" => ($notas === "" ? null : $notas),
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
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