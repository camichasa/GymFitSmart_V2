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


$alumnoId = filter_var($_POST["alumno_id"] ?? "", FILTER_VALIDATE_INT);
$ejercicioId = filter_var($_POST["ejercicio_id"] ?? "", FILTER_VALIDATE_INT);
$peso = filter_var($_POST["peso"] ?? "", FILTER_VALIDATE_FLOAT);
$repeticiones = filter_var($_POST["repeticiones"] ?? "", FILTER_VALIDATE_INT);


// Validación de los datos

if (
    $alumnoId === false ||
    $ejercicioId === false ||
    $peso === false || $peso < 0 || $peso > 1000 ||
    $repeticiones === false || $repeticiones < 1 || $repeticiones > 1000
) {

    header("Location: listado_alumnos.php?estado=invalido");
    exit();
}


try {

    // El ejercicio debe pertenecer a una rutina del alumno

    $consulta = $pdo->prepare(
        "SELECT e.id
         FROM ejercicios e
         INNER JOIN rutinas r ON r.id = e.rutina_id
         INNER JOIN usuarios u ON u.id = r.alumno_id
         WHERE e.id = ? AND r.alumno_id = ? AND u.rol = 'alumno'"
    );

    $consulta->execute([$ejercicioId, $alumnoId]);

    if (!$consulta->fetch()) {

        header("Location: listado_alumnos.php?estado=ejercicio");
        exit();
    }


    // Guardado del registro

    $insertar = $pdo->prepare(
        "INSERT INTO progreso
         (ejercicio_id, alumno_id, peso_utilizado, repeticiones_realizadas)
         VALUES (?, ?, ?, ?)"
    );

    $insertar->execute([$ejercicioId, $alumnoId, round($peso, 1), $repeticiones]);

    header("Location: listado_alumnos.php?estado=ok");
    exit();
} catch (Exception $e) {

    error_log("GymFit Smart - progreso no guardado: " . $e->getMessage());

    header("Location: listado_alumnos.php?estado=error");
    exit();
}
