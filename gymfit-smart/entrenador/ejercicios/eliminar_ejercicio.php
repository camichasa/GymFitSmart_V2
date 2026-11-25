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

$id = $_GET["id"];

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

    $sql = "DELETE ejercicios
            FROM ejercicios
            INNER JOIN rutinas
            ON ejercicios.rutina_id = rutinas.id
            WHERE ejercicios.id = :id
            AND rutinas.entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
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