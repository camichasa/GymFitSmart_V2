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

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("Location: listado_alumnos.php");
    exit();
}

require '../../config/conexion_bd.php';

$idAlumno = (int) $_GET["id"];

$sql = "SELECT usuarios.id,
               usuarios.nombre,
               usuarios.email,
               datos_alumno.peso,
               datos_alumno.altura,
               datos_alumno.edad,
               datos_alumno.sexo,
               datos_alumno.objetivo,
               datos_alumno.experiencia,
               datos_alumno.dias_entrenamiento,
               datos_alumno.contacto_emergencia_nombre,
               datos_alumno.contacto_emergencia_telefono
        FROM usuarios
        LEFT JOIN datos_alumno
            ON usuarios.id = datos_alumno.alumno_id
        WHERE usuarios.id = :id
        AND usuarios.rol = 'alumno'";

$resultado = $pdo->prepare($sql);
$resultado->execute(array(":id" => $idAlumno));
$alumno = $resultado->fetch(PDO::FETCH_ASSOC);

if (!$alumno) {
    header("Location: listado_alumnos.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/estilos_entrenador.css">
    <title>Editar alumno - GymFit Smart</title>
</head>

<body>

    <header>
        <h1 class="logo-texto">GYMFIT SMART</h1>
        <p>Editar datos del alumno</p>
    </header>

    <nav>
        <a href="../panel_entrenador.php">Inicio</a>
        <a href="listado_alumnos.php">Alumnos</a>
        <a href="../rutinas/listado_rutinas.php">Rutinas</a>
        <a href="../ejercicios/listado_ejercicios.php">Ejercicios</a>
        <a href="../reportes/reporte_rutinas.php">Reportes</a>
        <a href="#" id="logout-link">Cerrar sesión</a>
    </nav>

    <main class="contenedor">
        <section class="tarjeta">

            <h2>Editar alumno</h2>
            <p class="subtitulo">
                Modificá los datos de <?php echo htmlspecialchars($alumno["nombre"]); ?>.
            </p>

            <form action="actualizar_alumno.php" method="POST">

                <input type="hidden" name="id" value="<?php echo $alumno["id"]; ?>">

                <section class="campo">
                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($alumno["nombre"]); ?>"
                        required>
                </section>

                <section class="campo">
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($alumno["email"]); ?>"
                        required>
                </section>

                <section class="campo">
                    <label>Peso (kg)</label>
                    <input
                        type="number"
                        name="peso"
                        min="20"
                        max="300"
                        step="0.1"
                        value="<?php echo htmlspecialchars($alumno["peso"] ?? ""); ?>"
                        required>
                </section>

                <section class="campo">
                    <label>Altura (cm)</label>
                    <input
                        type="number"
                        name="altura"
                        min="100"
                        max="250"
                        value="<?php echo htmlspecialchars($alumno["altura"] ?? ""); ?>"
                        required>
                </section>

                <section class="campo">
                    <label>Edad</label>
                    <input
                        type="number"
                        name="edad"
                        min="10"
                        max="100"
                        value="<?php echo htmlspecialchars($alumno["edad"] ?? ""); ?>"
                        required>
                </section>

                <section class="campo">
                    <label>Sexo</label>
                    <select name="sexo" required>
                        <option value="">Seleccionar</option>
                        <option value="masculino" <?php echo $alumno["sexo"] === "masculino" ? "selected" : ""; ?>>Masculino</option>
                        <option value="femenino" <?php echo $alumno["sexo"] === "femenino" ? "selected" : ""; ?>>Femenino</option>
                        <option value="otro" <?php echo $alumno["sexo"] === "otro" ? "selected" : ""; ?>>Otro</option>
                    </select>
                </section>

                <section class="campo">
                    <label>Objetivo</label>
                    <select name="objetivo" required>
                        <option value="">Seleccionar</option>
                        <option value="Bajar peso" <?php echo $alumno["objetivo"] === "Bajar peso" ? "selected" : ""; ?>>Bajar peso</option>
                        <option value="Ganar musculo" <?php echo $alumno["objetivo"] === "Ganar musculo" ? "selected" : ""; ?>>Ganar músculo</option>
                        <option value="Mejorar mi salud" <?php echo $alumno["objetivo"] === "Mejorar mi salud" ? "selected" : ""; ?>>Mejorar mi salud</option>
                    </select>
                </section>

                <section class="campo">
                    <label>Experiencia</label>
                    <select name="experiencia">
                        <option value="">Seleccionar</option>
                        <option value="Ninguna" <?php echo $alumno["experiencia"] === "Ninguna" ? "selected" : ""; ?>>Ninguna</option>
                        <option value="Poca" <?php echo $alumno["experiencia"] === "Poca" ? "selected" : ""; ?>>Poca</option>
                        <option value="Media" <?php echo $alumno["experiencia"] === "Media" ? "selected" : ""; ?>>Media</option>
                        <option value="Mucha" <?php echo $alumno["experiencia"] === "Mucha" ? "selected" : ""; ?>>Mucha</option>
                    </select>
                </section>

                <section class="campo">
                    <label>Días de entrenamiento</label>
                    <select name="dias_entrenamiento">
                        <option value="">Seleccionar</option>
                        <?php for ($i = 1; $i <= 7; $i++) { ?>
                            <option
                                value="<?php echo $i; ?>"
                                <?php echo (string)$alumno["dias_entrenamiento"] === (string)$i ? "selected" : ""; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                </section>

                <section class="campo">
                    <label>Contacto de emergencia</label>
                    <input
                        type="text"
                        name="contacto_nombre"
                        maxlength="100"
                        value="<?php echo htmlspecialchars($alumno["contacto_emergencia_nombre"] ?? ""); ?>">
                </section>

                <section class="campo">
                    <label>Teléfono de emergencia</label>
                    <input
                        type="text"
                        name="contacto_telefono"
                        maxlength="15"
                        value="<?php echo htmlspecialchars($alumno["contacto_emergencia_telefono"] ?? ""); ?>"
                        required>
                </section>

                <input type="submit" value="GUARDAR CAMBIOS">

            </form>

            <section class="pie-formulario">
                <a href="ver_alumno.php?id=<?php echo $alumno["id"]; ?>">
                    Cancelar
                </a>
            </section>

        </section>
    </main>

    <footer>
        <p>GymFit Smart</p>
    </footer>


    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel" id="cancel-logout">Cancelar</button>
        </form>
    </dialog>

    <script>
        const PAGINA_LOGIN = '../../auth/cerrar_sesion.php';

        const logoutLink = document.getElementById('logout-link');
        const logoutModal = document.getElementById('logout-modal');
        const confirmLogout = document.getElementById('confirm-logout');

        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            logoutModal.showModal();
        });

        confirmLogout.addEventListener('click', () => {
            alert('Sesión cerrada con éxito');
            window.location.href = PAGINA_LOGIN;
        });
    </script>

</body>

</html>