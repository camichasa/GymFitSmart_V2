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
require '../../includes/dias_semana.php';


// Mensaje de resultado (lo envía guardar_progreso_alumno.php)

$mensajes = [
    'ok'        => ['ok', 'Progreso registrado correctamente.'],
    'invalido'  => ['error', 'Revisa los datos: selecciona un alumno y un ejercicio, el peso debe estar entre 0 y 1000 kg y las repeticiones entre 1 y 1000.'],
    'ejercicio' => ['error', 'Ese ejercicio no pertenece a las rutinas del alumno seleccionado.'],
    'error'     => ['error', 'No se pudo guardar el registro. Inténtalo de nuevo.']
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? null;


// Ejercicios de las rutinas de los alumnos

try {
    $stmt = $pdo->prepare("
        SELECT e.id, e.nombre, r.nombre AS rutina, r.dia, r.alumno_id
        FROM ejercicios e
        INNER JOIN rutinas r ON r.id = e.rutina_id
        INNER JOIN usuarios u ON u.id = r.alumno_id AND u.rol = 'alumno'
        ORDER BY r.dia IS NULL,
                 FIELD(r.dia, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
                 r.id, e.orden, e.id
    ");
    $stmt->execute();
    $ejercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $ejercicios = array();
}

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

        <!-- Registrar progreso -->
        <section>
            <h2>Registrar progreso del Alumno</h2>
            <p>Registra el peso y las repeticiones de un alumno. El registro aparecerá en la pantalla "Mi progreso" de ese alumno.</p>

            <?php if ($mensaje) { ?>
                <p class="progreso-msg progreso-msg-<?php echo $mensaje[0]; ?>">
                    <?php echo htmlspecialchars($mensaje[1]); ?>
                </p>
            <?php } ?>

            <?php if (count($alumnos) == 0) { ?>

                <p>Todavía no hay alumnos registrados.</p>

            <?php } else { ?>

                <form class="progreso-form" action="guardar_progreso_alumno.php" method="POST">

                    <label>Alumno
                        <select name="alumno_id" id="select-alumno" required>
                            <option value="">Seleccionar alumno</option>
                            <?php foreach ($alumnos as $alumno) { ?>
                                <option value="<?php echo (int)$alumno['id']; ?>">
                                    <?php echo htmlspecialchars($alumno['nombre']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>

                    <label>Ejercicio
                        <select name="ejercicio_id" id="select-ejercicio" required disabled>
                            <option value="">Primero selecciona un alumno</option>
                            <?php foreach ($ejercicios as $e) { ?>
                                <option value="<?php echo (int)$e['id']; ?>" data-alumno="<?php echo (int)$e['alumno_id']; ?>" hidden disabled>
                                    <?php
                                    echo htmlspecialchars(
                                        ($e['dia'] ? nombreDia($e['dia']) . ' · ' : '') .
                                            $e['rutina'] . ' — ' . $e['nombre']
                                    );
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>

                    <label>Peso utilizado (kg)
                        <input type="number" name="peso" min="0" max="1000" step="0.1" placeholder="0 si es con su peso corporal" required>
                    </label>

                    <label>Repeticiones realizadas
                        <input type="number" name="repeticiones" min="1" max="1000" required>
                    </label>

                    <button class="btn-guardar" type="submit">REGISTRAR</button>

                </form>

            <?php } ?>
        </section>


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


    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel" id="cancel-logout">Cancelar</button>
        </form>
    </dialog>

    <script>
        const PAGINA_LOGIN = '../../auth/cerrar_sesion.php';

        // Filtro de ejercicios según el alumno elegido
        const selectAlumno = document.getElementById('select-alumno');
        const selectEjercicio = document.getElementById('select-ejercicio');

        if (selectAlumno && selectEjercicio) {
            const opcionesEjercicio = selectEjercicio.querySelectorAll('option[data-alumno]');

            selectAlumno.addEventListener('change', () => {
                const alumnoId = selectAlumno.value;
                let disponibles = 0;

                selectEjercicio.value = '';

                opcionesEjercicio.forEach((op) => {
                    const coincide = op.dataset.alumno === alumnoId;
                    op.hidden = !coincide;
                    op.disabled = !coincide;
                    if (coincide) disponibles++;
                });

                const primera = selectEjercicio.options[0];

                if (alumnoId === '') {
                    primera.textContent = 'Primero selecciona un alumno';
                    selectEjercicio.disabled = true;
                } else if (disponibles === 0) {
                    primera.textContent = 'Este alumno no tiene ejercicios asignados';
                    selectEjercicio.disabled = true;
                } else {
                    primera.textContent = 'Seleccionar ejercicio';
                    selectEjercicio.disabled = false;
                }
            });
        }

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