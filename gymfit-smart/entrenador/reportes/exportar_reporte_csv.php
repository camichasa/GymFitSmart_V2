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

    $sql = "SELECT rutinas.nombre,
                   rutinas.descripcion,
                   rutinas.fecha_creacion,
                   usuarios.nombre AS nombre_alumno,
                   usuarios.email AS email_alumno
            FROM rutinas
            INNER JOIN usuarios
            ON rutinas.alumno_id = usuarios.id
            WHERE rutinas.entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );


    header("Content-Type: text/csv; charset=UTF-8");

    header(
        "Content-Disposition: attachment; filename=reporte_gymfit.csv"
    );


    $archivo = fopen("php://output", "w");


    fputcsv(
        $archivo,
        array(
            "Rutina",
            "Alumno",
            "Email",
            "Descripcion",
            "Fecha"
        ),
        ";"
    );


    while (
        $registro =
        $resultado->fetch(
            PDO::FETCH_ASSOC
        )
    ) {

        fputcsv(
            $archivo,
            array(
                $registro["nombre"],
                $registro["nombre_alumno"],
                $registro["email_alumno"],
                $registro["descripcion"],
                $registro["fecha_creacion"]
            ),
            ";"
        );
    }


    fclose($archivo);

    $resultado->closeCursor();

    $base = null;

    exit();

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

}

?>