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

require '../../config/conexion_bd.php';

try {
    $sql = "SELECT usuarios.id,
                   usuarios.nombre,
                   usuarios.email,
                   usuarios.fecha_registro,
                   datos_alumno.peso,
                   datos_alumno.altura,
                   datos_alumno.objetivo
            FROM usuarios
            LEFT JOIN datos_alumno
                ON usuarios.id = datos_alumno.alumno_id
            WHERE usuarios.rol = 'alumno'
            ORDER BY usuarios.nombre ASC";

    $resultado = $pdo->prepare($sql);
    $resultado->execute();
    $alumnos = $resultado->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $alumnos = array();
    $error = "No se pudo cargar el listado de alumnos.";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/estilos_entrenador.css">
    <title>Alumnos - GymFit Smart</title>
</head>

<body>

    <header>
        <h1 class="logo-texto">GYMFIT SMART</h1>
        <p>Gestión de alumnos</p>
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
            <h2>Listado de alumnos</h2>
            <p>Desde aquí podés visualizar y editar los datos de los alumnos registrados.</p>

            <?php if (isset($error)) { ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php } elseif (count($alumnos) == 0) { ?>
                <p>No hay alumnos registrados.</p>
            <?php } else { ?>

                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Peso</th>
                            <th>Altura</th>
                            <th>Objetivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($alumnos as $alumno) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($alumno["email"]); ?></td>
                                <td>
                                    <?php
                                    echo $alumno["peso"] !== null
                                        ? htmlspecialchars($alumno["peso"]) . " kg"
                                        : "Sin datos";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $alumno["altura"] !== null
                                        ? htmlspecialchars($alumno["altura"]) . " cm"
                                        : "Sin datos";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $alumno["objetivo"] !== null
                                        ? htmlspecialchars($alumno["objetivo"])
                                        : "Sin datos";
                                    ?>
                                </td>
                                <td>
                                    <a href="ver_alumno.php?id=<?php echo $alumno["id"]; ?>">
                                        Ver
                                    </a>
                                    |
                                    <a href="editar_alumno.php?id=<?php echo $alumno["id"]; ?>">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            <?php } ?>
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
