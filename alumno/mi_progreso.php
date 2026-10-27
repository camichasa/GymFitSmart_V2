<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header("Location: ../auth/iniciar_sesion.html");
    exit();
}

if ($_SESSION["rol"] != "alumno") {

    header("Location: ../entrenador/panel_entrenador.php");
    exit();
}

require '../config/conexion_bd.php';
require '../includes/dias_semana.php';

$alumnoId = (int)$_SESSION['id_usuario'];


/* Muestra el peso sin ceros de más: 60 -> "60 kg", 22.5 -> "22.5 kg", 0 -> "Peso corporal" */
function formatoPeso($peso)
{
    $peso = (float)$peso;

    if ($peso == 0) {
        return 'Peso corporal';
    }

    return rtrim(rtrim(number_format($peso, 1, '.', ''), '0'), '.') . ' kg';
}


/* MENSAJE DESPUÉS DE REGISTRAR (lo envía guardar_progreso.php) */

$mensajes = [
    'ok'        => ['ok', 'Progreso registrado correctamente.'],
    'invalido'  => ['error', 'Revisa los datos: el peso debe estar entre 0 y 1000 kg y las repeticiones entre 1 y 1000.'],
    'ejercicio' => ['error', 'Ese ejercicio no pertenece a tus rutinas.'],
    'error'     => ['error', 'No se pudo guardar el registro. Inténtalo de nuevo.']
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? null;


/* EJERCICIOS DE LAS RUTINAS DEL ALUMNO (para el formulario) */

$stmt = $pdo->prepare("
    SELECT e.id, e.nombre, r.nombre AS rutina, r.dia
    FROM ejercicios e
    INNER JOIN rutinas r ON r.id = e.rutina_id
    WHERE r.alumno_id = ?
    ORDER BY r.dia IS NULL,
             FIELD(r.dia, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
             r.id, e.orden, e.id
");
$stmt->execute([$alumnoId]);
$ejercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* REGISTROS DE PROGRESO DEL ALUMNO (del más antiguo al más reciente) */

$stmt = $pdo->prepare("
    SELECT p.ejercicio_id, p.peso_utilizado, p.repeticiones_realizadas, p.registrado_en,
           e.nombre AS ejercicio, r.nombre AS rutina
    FROM progreso p
    INNER JOIN ejercicios e ON e.id = p.ejercicio_id
    INNER JOIN rutinas r ON r.id = e.rutina_id
    WHERE p.alumno_id = ?
    ORDER BY p.registrado_en ASC, p.id ASC
");
$stmt->execute([$alumnoId]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ESTADÍSTICAS POR EJERCICIO */

$estadisticas = [];

foreach ($registros as $r) {

    $id = (int)$r['ejercicio_id'];
    $peso = (float)$r['peso_utilizado'];

    if (!isset($estadisticas[$id])) {
        $estadisticas[$id] = [
            'nombre'    => $r['ejercicio'],
            'rutina'    => $r['rutina'],
            'registros' => 0,
            'primero'   => $peso,
            'ultimo'    => $peso,
            'mejor'     => $peso
        ];
    }

    $estadisticas[$id]['registros']++;
    $estadisticas[$id]['ultimo'] = $peso;

    if ($peso > $estadisticas[$id]['mejor']) {
        $estadisticas[$id]['mejor'] = $peso;
    }
}

$totalRegistros = count($registros);
$ultimoRegistro = $totalRegistros > 0 ? $registros[$totalRegistros - 1] : null;
$historial = array_slice(array_reverse($registros), 0, 15);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Mi progreso
    </title>
    <link rel="stylesheet" href="../assets/css/estilos_alumno.css">
</head>

<body>

    <header>
        <!-- icono del menú desplegable -->
        <button class="hamburguesa" id="btn-menu" aria-label="Abrir menú" aria-expanded="false">
            <span></span> <!-- es para crar las lineas del boton de menu -->
            <span></span>
            <span></span>
        </button>
        <a class="logo" href="inicio_alumno.php">GymFit Smart</a>
    </header>


    <button class="menu-overlay" id="menu-overlay" aria-label="Cerrar menú" tabindex="-1"></button> <!-- Oscurece el fondo cuando se abre el menu -->


    <!-- Menú deplegable -->
    <aside class="menu-lateral" id="menu-lateral">
        <button class="menu-cerrar" id="btn-cerrar" aria-label="Cerrar menú">&times;</button>
        <ul>
            <li><a href="inicio_alumno.php">Inicio</a></li>
            <li><a href="mi_progreso.php">Mi progreso</a></li>
            <li><a href="mis_datos.php">Mis datos</a></li>
            <li><a href="mi_rutina.php">Mi rutina</a></li>
            <li><a href="#" id="logout-link">Cerrar sesion</a></li>
        </ul>
    </aside>

    <main>

        <section class="main-content">
            <h1>Mi progreso</h1>
            <p>Registra el peso y las repeticiones que lograste en cada ejercicio y mira cómo vas mejorando.</p>

            <?php if ($mensaje) { ?>
                <p class="progreso-msg progreso-msg-<?php echo $mensaje[0]; ?>">
                    <?php echo htmlspecialchars($mensaje[1]); ?>
                </p>
            <?php } ?>
        </section>


        <!-- RESUMEN -->
        <section class="progreso-resumen">

            <section class="progreso-dato">
                <strong><?php echo $totalRegistros; ?></strong>
                <span>Registros</span>
            </section>

            <section class="progreso-dato">
                <strong><?php echo count($estadisticas); ?></strong>
                <span>Ejercicios con registro</span>
            </section>

            <section class="progreso-dato">
                <strong>
                    <?php
                    echo $ultimoRegistro
                        ? date('d/m/Y', strtotime($ultimoRegistro['registrado_en']))
                        : '-';
                    ?>
                </strong>
                <span>Último registro</span>
            </section>

        </section>


        <!-- REGISTRAR -->
        <section class="progreso-box">
            <h2>Registrar progreso</h2>

            <?php if (count($ejercicios) === 0) { ?>

                <p>Todavía no tienes ejercicios asignados. Cuando tu entrenador te asigne una rutina, podrás registrar tu progreso aquí.</p>

            <?php } else { ?>

                <form class="progreso-form" action="guardar_progreso.php" method="POST">

                    <label>Ejercicio
                        <select name="ejercicio_id" required>
                            <option value="">Seleccionar ejercicio</option>
                            <?php foreach ($ejercicios as $e) { ?>
                                <option value="<?php echo (int)$e['id']; ?>">
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
                        <input type="number" name="peso" min="0" max="1000" step="0.1" placeholder="0 si es con tu peso corporal" required>
                    </label>

                    <label>Repeticiones realizadas
                        <input type="number" name="repeticiones" min="1" max="1000" required>
                    </label>

                    <button class="btn-guardar" type="submit">REGISTRAR</button>

                </form>

            <?php } ?>
        </section>


        <?php if ($totalRegistros > 0) { ?>

            <!-- EVOLUCIÓN -->
            <section class="progreso-box">
                <h2>Evolución por ejercicio</h2>

                <section class="progreso-tabla">
                    <table>
                        <tr>
                            <th>Ejercicio</th>
                            <th>Registros</th>
                            <th>Primer peso</th>
                            <th>Último peso</th>
                            <th>Mejor peso</th>
                            <th>Cambio</th>
                        </tr>

                        <?php foreach ($estadisticas as $est) {

                            $dif = $est['ultimo'] - $est['primero'];

                            if (abs($dif) < 0.05) {
                                $cambio = 'Sin cambio';
                                $clase = '';
                            } elseif ($dif > 0) {
                                $cambio = '+' . rtrim(rtrim(number_format($dif, 1, '.', ''), '0'), '.') . ' kg';
                                $clase = 'cambio-sube';
                            } else {
                                $cambio = '-' . rtrim(rtrim(number_format(abs($dif), 1, '.', ''), '0'), '.') . ' kg';
                                $clase = 'cambio-baja';
                            }

                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($est['nombre']); ?></td>
                                <td><?php echo $est['registros']; ?></td>
                                <td><?php echo formatoPeso($est['primero']); ?></td>
                                <td><?php echo formatoPeso($est['ultimo']); ?></td>
                                <td><?php echo formatoPeso($est['mejor']); ?></td>
                                <td class="<?php echo $clase; ?>"><?php echo $cambio; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </section>
            </section>


            <!-- HISTORIAL -->
            <section class="progreso-box">
                <h2>Últimos registros</h2>

                <section class="progreso-tabla">
                    <table>
                        <tr>
                            <th>Fecha</th>
                            <th>Ejercicio</th>
                            <th>Rutina</th>
                            <th>Peso</th>
                            <th>Repeticiones</th>
                        </tr>

                        <?php foreach ($historial as $h) { ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($h['registrado_en'])); ?></td>
                                <td><?php echo htmlspecialchars($h['ejercicio']); ?></td>
                                <td><?php echo htmlspecialchars($h['rutina']); ?></td>
                                <td><?php echo formatoPeso($h['peso_utilizado']); ?></td>
                                <td><?php echo (int)$h['repeticiones_realizadas']; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </section>
            </section>

        <?php } ?>

    </main>

    <!-- confirmación para cerrar sesión -->
    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel" id="cancel-logout">Cancelar</button>
        </form>
    </dialog>

    <footer>todos los derechos de Gymfit Smart estan reservados.</footer>

    <script>
        // Página a la que se vuelve al cerrar sesión (ajustar a la ruta real del login)
        const PAGINA_LOGIN = '../auth/cerrar_sesion.php';

        // Menú desplegable
        const btnMenu = document.getElementById('btn-menu');
        const btnCerrar = document.getElementById('btn-cerrar');
        const menuLateral = document.getElementById('menu-lateral');
        const overlay = document.getElementById('menu-overlay');

        function abrirMenu() {
            menuLateral.classList.add('active');
            overlay.classList.add('active');
            btnMenu.setAttribute('aria-expanded', 'true');
        }

        function cerrarMenu() {
            menuLateral.classList.remove('active');
            overlay.classList.remove('active');
            btnMenu.setAttribute('aria-expanded', 'false');
        }

        btnMenu.addEventListener('click', abrirMenu);
        btnCerrar.addEventListener('click', cerrarMenu);
        overlay.addEventListener('click', cerrarMenu);

        // Cerrar el menú con la tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarMenu();
        });

        // Cerrar sesión
        const logoutLink = document.getElementById('logout-link');
        const logoutModal = document.getElementById('logout-modal');
        const confirmLogout = document.getElementById('confirm-logout');

        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            cerrarMenu();
            logoutModal.showModal();
        });

        confirmLogout.addEventListener('click', () => {
            alert('Sesión cerrada con éxito');
            window.location.href = PAGINA_LOGIN;
        });
    </script>
</body>

</html>