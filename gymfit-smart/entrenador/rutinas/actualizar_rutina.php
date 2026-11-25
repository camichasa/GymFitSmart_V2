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


// Importación de librería PHPMailer

require("../../libs/PHPMailer/Exception.php");
require("../../libs/PHPMailer/PHPMailer.php");
require("../../libs/PHPMailer/SMTP.php");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$id = $_POST["id"];
$nombre = $_POST["nombre"];
$descripcion = $_POST["descripcion"];
$alumno_id = $_POST["alumno_id"];
$dia = $_POST["dia"];


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


    // Actualización de la rutina

    $sql = "UPDATE rutinas
            SET nombre = :nombre,
                descripcion = :descripcion,
                alumno_id = :alumno_id,
                dia = :dia
            WHERE id = :id
            AND entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":nombre" => $nombre,
            ":descripcion" => $descripcion,
            ":alumno_id" => $alumno_id,
            ":dia" => $dia,
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );


    // Datos del alumno dueño de la rutina

    $sqlAlumno = "SELECT usuarios.nombre AS nombre_alumno,
                         usuarios.email AS email_alumno,
                         rutinas.nombre AS nombre_rutina
                  FROM rutinas
                  INNER JOIN usuarios
                  ON rutinas.alumno_id = usuarios.id
                  WHERE rutinas.id = :id
                  AND rutinas.entrenador_id = :entrenador_id";

    $resultadoAlumno = $base->prepare($sqlAlumno);

    $resultadoAlumno->execute(
        array(
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );

    $alumno = $resultadoAlumno->fetch(
        PDO::FETCH_ASSOC
    );


    // Envío de correo de aviso al alumno

    if ($alumno) {

        try {

            // Configuración de la cuenta de correo

            $archivoConfig = "../../config/correo_config.php";

            if (!file_exists($archivoConfig)) {

                throw new Exception(
                    "Falta el archivo config/correo_config.php"
                );
            }

            $config_correo = require($archivoConfig);

            $mail = new PHPMailer(true);

            $mail->isSMTP();

            $mail->Host = $config_correo["host"];

            $mail->SMTPAuth = true;

            $mail->Username = $config_correo["usuario"];

            $mail->Password = $config_correo["clave"];


            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = $config_correo["puerto"];

            $mail->CharSet = "UTF-8";



            $mail->setFrom(
                $config_correo["usuario"],
                $config_correo["nombre_remitente"]
            );



            $mail->addAddress(
                $alumno["email_alumno"],
                $alumno["nombre_alumno"]
            );



            $mail->isHTML(true);

            $mail->Subject =
                "GymFit Smart - Rutina actualizada";

            $mail->Body =
                "<h2>GymFit Smart</h2>" .
                "<p>Hola " .
                htmlspecialchars(
                    $alumno["nombre_alumno"]
                ) .
                ".</p>" .
                "<p>Tu entrenador actualizó tu rutina:</p>" .
                "<p><strong>" .
                htmlspecialchars(
                    $alumno["nombre_rutina"]
                ) .
                "</strong></p>" .
                "<p>Ingresá a GymFit Smart para consultar los cambios.</p>";


            $mail->AltBody =
                "Hola " .
                $alumno["nombre_alumno"] .
                ". Tu entrenador actualizó tu rutina " .
                $alumno["nombre_rutina"] .
                ". Ingresa a GymFit Smart para consultar los cambios.";


            $mail->send();

        } catch (Exception $e) {

            // Si el correo falla, la rutina ya quedó actualizada (no se cancela)

            error_log(
                "GymFit Smart - correo no enviado: " .
                $e->getMessage()
            );

        }

    }


    $resultado->closeCursor();
    $resultadoAlumno->closeCursor();

    $base = null;


    header("Location: listado_rutinas.php");
    exit();


} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

}

?>