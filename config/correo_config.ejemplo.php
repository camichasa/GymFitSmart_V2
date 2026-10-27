<?php

/*
    CONFIGURACIÓN DEL ENVÍO DE CORREOS (Gmail + PHPMailer)

    1. Activá la verificación en dos pasos en la cuenta de Gmail.
    2. En la cuenta de Google creá una "Contraseña de aplicación".
    3. Pegá esa clave de 16 caracteres en "clave" (sin espacios).
    4. "usuario" tiene que ser la misma cuenta de Gmail que generó la clave.

    Copiá este archivo como correo_config.php y completalo. El archivo
    correo_config.php está excluido del repositorio en .gitignore.
*/

return array(
    "host" => "smtp.gmail.com",
    "puerto" => 587,
    "usuario" => "gymfitsmart@gmail.com",
    "clave" => "CLAVE_DE_EJEMPLO",
    "nombre_remitente" => "GymFit Smart"
);
