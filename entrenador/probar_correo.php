<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/iniciar_sesion.html");
    exit();
}

if ($_SESSION["rol"] != "entrenador") {

    header("Location: ../alumno/inicio_alumno.php");
    exit();
}


/* ARCHIVOS DE PHPMAILER */

require("../libs/PHPMailer/Exception.php");
require("../libs/PHPMailer/PHPMailer.php");
require("../libs/PHPMailer/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require("../config/conexion_bd.php");


/*
    PÁGINA DE PRUEBA: envía un correo al email del entrenador
    que tiene la sesión iniciada, y muestra el resultado real
    (o el motivo del error) para comprobar la configuración.

    Se puede borrar cuando el correo ya funcione.
*/

$titulo = "";
$mensaje = "";

try {

    $archivoConfig = "../config/correo_config.php";

    if (!file_exists($archivoConfig)) {

        throw new Exception(
            "Falta el archivo config/correo_config.php"
        );
    }

    $config_correo = require($archivoConfig);

    $consulta = $pdo->prepare(
        "SELECT nombre, email FROM usuarios WHERE id = :id"
    );

    $consulta->execute(array(":id" => $_SESSION["id_usuario"]));

    $entrenador = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$entrenador) {

        throw new Exception("No se encontró tu usuario.");
    }

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = $config_correo["host"];
    $mail->SMTPAuth = true;
    $mail->Username = $config_correo["usuario"];
    $mail->Password = $config_correo["clave"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config_correo["puerto"];
    $mail->CharSet = "UTF-8";

    $mail->setFrom(
        $config_correo["usuario"],
        $config_correo["nombre_remitente"]
    );

    $mail->addAddress($entrenador["email"], $entrenador["nombre"]);

    $mail->isHTML(true);
    $mail->Subject = "GymFit Smart - Correo de prueba";
    $mail->Body = "<h2>GymFit Smart</h2>" .
        "<p>Si estás leyendo esto, el envío de correos funciona.</p>";
    $mail->AltBody = "Si estás leyendo esto, el envío de correos funciona.";

    $mail->send();

    $titulo = "Correo enviado";
    $mensaje = "Se envió un correo de prueba a " . $entrenador["email"] .
        ". Revisá también la carpeta de spam.";
} catch (Exception $e) {

    $titulo = "No se pudo enviar el correo";
    $mensaje = $e->getMessage();
} catch (PDOException $e) {

    $titulo = "Error de base de datos";
    $mensaje = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/estilos_autenticacion.css">

    <title>Prueba de correo - GymFit Smart</title>
</head>

<body>

    <main class="contenedor">

        <section class="tarjeta mensaje">

            <h1 class="logo-texto">GYMFIT SMART</h1>

            <h2><?php echo htmlspecialchars($titulo); ?></h2>

            <p><?php echo htmlspecialchars($mensaje); ?></p>

            <a href="panel_entrenador.php" class="btn-link">
                VOLVER AL PANEL
            </a>

        </section>

    </main>

</body>

</html>
