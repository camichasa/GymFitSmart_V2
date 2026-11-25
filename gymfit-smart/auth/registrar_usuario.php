<?php

$nombre = $_POST["nombre"];
$email = $_POST["email"];
$contra = $_POST["contra"];
$rol = $_POST["rol"];

$pass_cifrada = password_hash(
    $contra,
    PASSWORD_DEFAULT,
    array("cost" => 12)
);

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

    // Verificación de email duplicado
    $sqlBuscar = "SELECT * FROM usuarios
                  WHERE email = :email";

    $resultadoBuscar = $base->prepare($sqlBuscar);

    $resultadoBuscar->execute(
        array(
            ":email" => $email
        )
    );

    $usuarioExistente = $resultadoBuscar->fetch(PDO::FETCH_ASSOC);

    if ($usuarioExistente) {

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/estilos_autenticacion.css">

    <title>Email registrado - GymFit Smart</title>
</head>

<body>

    <main class="contenedor">

        <section class="tarjeta mensaje">

            <h1 class="logo-texto">GYMFIT SMART</h1>

            <h2>El email ya está registrado</h2>

            <p>
                Ya existe una cuenta asociada a ese correo electrónico.
            </p>

            <a href="iniciar_sesion.html" class="btn-link">
                IR AL LOGIN
            </a>

            <br><br>

            <a href="formulario_registro.html">
                Volver al registro
            </a>

        </section>

    </main>

</body>

</html>

<?php

    } else {

        // Registro del nuevo usuario
        $sql = "INSERT INTO usuarios
                (nombre, email, contraseña, rol, entrenador_id)
                VALUES
                (:nombre, :email, :contra, :rol, NULL)";

        $resultado = $base->prepare($sql);

        $resultado->execute(
            array(
                ":nombre" => $nombre,
                ":email" => $email,
                ":contra" => $pass_cifrada,
                ":rol" => $rol
            )
        );

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/estilos_autenticacion.css">

    <title>Registro exitoso - GymFit Smart</title>
</head>

<body>

    <main class="contenedor">

        <section class="tarjeta mensaje">

            <h1 class="logo-texto">GYMFIT SMART</h1>

            <h2>¡Registro exitoso!</h2>

            <p>
                Usuario registrado correctamente.
            </p>

            <a href="iniciar_sesion.html" class="btn-link">
                IR AL LOGIN
            </a>

        </section>

    </main>

</body>

</html>

<?php

        $resultado->closeCursor();
    }

    $resultadoBuscar->closeCursor();

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

} finally {

    $base = null;
}

?>