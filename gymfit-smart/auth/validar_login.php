<?php

session_start();

$email = $_POST["email"];
$contrasenia = $_POST["contra"];

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

    $sql = "SELECT *
            FROM usuarios
            WHERE email = :email";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":email" => $email
        )
    );

    $registro = $resultado->fetch(PDO::FETCH_ASSOC);

    if (
        $registro &&
        password_verify(
            $contrasenia,
            $registro["contraseña"]
        )
    ) {

        $_SESSION["id_usuario"] = $registro["id"];
        $_SESSION["nombre"] = $registro["nombre"];
        $_SESSION["rol"] = $registro["rol"];

        if ($registro["rol"] == "entrenador") {

            header("Location: ../entrenador/panel_entrenador.php");
            exit();
        } else {

            header("Location: ../alumno/inicio_alumno.php");
            exit();
        }
    }
} catch (Exception $e) {

    echo "Error: " . $e->getMessage();
} finally {

    $base = null;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/estilos_autenticacion.css">

    <title>GymFit Smart</title>
</head>

<body>

    <main class="contenedor">

        <section class="tarjeta mensaje">

            <h1 class="logo-texto">GYMFIT SMART</h1>

            <h2>No pudimos iniciar sesión</h2>

            <p>Email o contraseña incorrectos.</p>

            <a href="iniciar_sesion.html" class="btn-link">
                VOLVER A INTENTAR
            </a>

        </section>

    </main>

</body>

</html>