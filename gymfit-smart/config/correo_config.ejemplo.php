<?php

// Configuración de correo (Gmail + PHPMailer)
// usuario: cuenta de Gmail con verificación en dos pasos
// clave: "Contraseña de aplicación" de 16 caracteres, sin espacios
// Copiar como correo_config.php y completar (está excluido en .gitignore)

return array(
    "host" => "smtp.gmail.com",
    "puerto" => 587,
    "usuario" => "gymfitsmart@gmail.com",
    "clave" => "CLAVE_DE_EJEMPLO",
    "nombre_remitente" => "GymFit Smart"
);
