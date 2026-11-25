<?php

// Configuración de correo (Gmail + PHPMailer)
// usuario: cuenta de Gmail con verificación en dos pasos
// clave: "Contraseña de aplicación" de 16 caracteres, sin espacios
// Este archivo contiene una clave: no subir a GitHub (excluido en .gitignore)

return array(
    "host" => "smtp.gmail.com",
    "puerto" => 587,
    "usuario" => "gymfitsmart@gmail.com",
    "clave" => "PEGAR_ACA_LA_CONTRASENA_DE_APLICACION",
    "nombre_remitente" => "GymFit Smart"
);
