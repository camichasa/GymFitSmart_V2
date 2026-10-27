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
               usuarios.fecha_registro,
               datos_alumno.peso,
               datos_alumno.altura,
               datos_alumno.edad,
               datos_alumno.sexo,
               datos_alumno.objetivo,
               datos_alumno.experiencia,
               datos_alumno.dias_entrenamiento,
               datos_alumno.contacto_emergencia_nombre,
               datos_alumno.contacto_emergencia_telefono,
               datos_alumno.actualizado_en
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

function mostrarDato($valor, $unidad = "")
{
    if ($valor === null || $valor === "") {
        return "Sin datos";
    }

    return htmlspecialchars($valor) . $unidad;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/estilos_entrenador.css">
    <title>Datos del alumno - GymFit Smart</title>
</head>

<body>

    <header>
        <h1 class="logo-texto">GYMFIT SMART</h1>
        <p>Datos del alumno</p>
    </header>

    <nav>
        <a href="../panel_entrenador.php">Inicio</a>
        <a href="listado_alumnos.php">Alumnos</a>
        <a href="../rutinas/listado_rutinas.php">Rutinas</a>
        <a href="../ejercicios/listado_ejercicios.php">Ejercicios</a>
        <a href="../reportes/reporte_rutinas.php">Reportes</a>
        <a href="#" id="logout-link">Cerrar sesión</a>
    </nav>

    <main>
        <section>
            <h2><?php echo htmlspecialchars($alumno["nombre"]); ?></h2>

            <h3>Datos de la cuenta</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($alumno["email"]); ?></p>
            <p><strong>Fecha de registro:</strong> <?php echo htmlspecialchars($alumno["fecha_registro"]); ?></p>

            <h3>Datos físicos</h3>
            <p><strong>Peso:</strong> <?php echo mostrarDato($alumno["peso"], " kg"); ?></p>
            <p><strong>Altura:</strong> <?php echo mostrarDato($alumno["altura"], " cm"); ?></p>
            <p><strong>Edad:</strong> <?php echo mostrarDato($alumno["edad"]); ?></p>
            <p><strong>Sexo:</strong> <?php echo mostrarDato($alumno["sexo"]); ?></p>

            <h3>Objetivo y experiencia</h3>
            <p><strong>Objetivo:</strong> <?php echo mostrarDato($alumno["objetivo"]); ?></p>
            <p><strong>Experiencia:</strong> <?php echo mostrarDato($alumno["experiencia"]); ?></p>
            <p><strong>Días de entrenamiento:</strong> <?php echo mostrarDato($alumno["dias_entrenamiento"]); ?></p>

            <h3>Contacto de emergencia</h3>
            <p><strong>Nombre:</strong> <?php echo mostrarDato($alumno["contacto_emergencia_nombre"]); ?></p>
            <p><strong>Teléfono:</strong> <?php echo mostrarDato($alumno["contacto_emergencia_telefono"]); ?></p>

            <br>

            <a class="btn-link" href="editar_alumno.php?id=<?php echo $alumno["id"]; ?>">
                EDITAR ALUMNO
            </a>

            <div class="pie-formulario">
                <a href="listado_alumnos.php">Volver al listado</a>
            </div>
        </section>
    </main>

    <footer>
        <p>GymFit Smart</p>
    </footer>


    <!-- Modal de confirmación para cerrar sesión -->
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
