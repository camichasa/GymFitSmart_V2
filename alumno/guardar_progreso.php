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

    header("Location: mi_progreso.php");
    exit();
}

require '../config/conexion_bd.php';


$alumnoId = (int)$_SESSION["id_usuario"];

$ejercicioId = filter_var($_POST["ejercicio_id"] ?? "", FILTER_VALIDATE_INT);
$peso = filter_var($_POST["peso"] ?? "", FILTER_VALIDATE_FLOAT);
$repeticiones = filter_var($_POST["repeticiones"] ?? "", FILTER_VALIDATE_INT);


/* VALIDACIÓN (mismos límites que el formulario) */

if (
    $ejercicioId === false ||
    $peso === false || $peso < 0 || $peso > 1000 ||
    $repeticiones === false || $repeticiones < 1 || $repeticiones > 1000
) {

    header("Location: mi_progreso.php?estado=invalido");
    exit();
}


try {

    /* EL EJERCICIO TIENE QUE SER DE UNA RUTINA DE ESTE ALUMNO */

    $consulta = $pdo->prepare(
        "SELECT e.id
         FROM ejercicios e
         INNER JOIN rutinas r ON r.id = e.rutina_id
         WHERE e.id = ? AND r.alumno_id = ?"
    );

    $consulta->execute([$ejercicioId, $alumnoId]);

    if (!$consulta->fetch()) {

        header("Location: mi_progreso.php?estado=ejercicio");
        exit();
    }


    /* GUARDAR EL REGISTRO (la fecha la pone la base de datos) */

    $insertar = $pdo->prepare(
        "INSERT INTO progreso
         (ejercicio_id, alumno_id, peso_utilizado, repeticiones_realizadas)
         VALUES (?, ?, ?, ?)"
    );

    $insertar->execute([$ejercicioId, $alumnoId, round($peso, 1), $repeticiones]);

    header("Location: mi_progreso.php?estado=ok");
    exit();
} catch (Exception $e) {

    error_log("GymFit Smart - progreso no guardado: " . $e->getMessage());

    header("Location: mi_progreso.php?estado=error");
    exit();
}
