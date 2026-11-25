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

    $sql = "DELETE FROM rutinas
            WHERE id = :id
            AND entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
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