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
require '../includes/funciones_rutina.php';
$rutina = obtenerRutina($pdo, (int)$_SESSION['id_usuario']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Ejercicio</title>
    <link rel="stylesheet" href="../assets/css/estilos_alumno.css">
</head>

<body>


    <header>
        <button class="hamburguesa" id="btn-menu" aria-label="Abrir menú" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <a class="logo" href="inicio_alumno.php">GymFit Smart</a>
    </header>

    <button class="menu-overlay" id="menu-overlay" aria-label="Cerrar menú" tabindex="-1"></button>

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
            <h1>Mi rutina semanal</h1>
            <p>Aquí puedes visualizar tus rutinas semanales asignadas por tu entrenador.</p>
            <button class="btn-pdf" type="button" id="open-pdf-modal">Descargar rutina en PDF</button>
        </section>

        <ul class="days-container">
            <li><button class="day-box" data-dia="lunes">LUN</button></li>
            <li><button class="day-box" data-dia="martes">MAR</button></li>
            <li><button class="day-box" data-dia="miercoles">MIÉ</button></li>
            <li><button class="day-box" data-dia="jueves">JUE</button></li>
            <li><button class="day-box" data-dia="viernes">VIE</button></li>
            <li><button class="day-box" data-dia="sabado">SAB</button></li>
            <li><button class="day-box" data-dia="domingo">DOM</button></li>
        </ul>

        <section class="workout-details" id="detalle"></section>
    </main>
    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" type="button" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel">Cancelar</button>
        </form>
    </dialog>
    <dialog id="pdf-modal">
        <p>¿Quiere descargar su rutina semanal en PDF?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" type="button" id="confirm-pdf">Sí, descargar</button>
            <button class="btn btn-cancel">Cancelar</button>
        </form>
    </dialog>

    <footer>todos los derechos de Gymfit Smart estan reservados.</footer>

    <script>
        // Redirección al cerrar sesión
        const PAGINA_LOGIN = "../auth/cerrar_sesion.php";

        // Menú lateral
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

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarMenu();
        });

        // Rutina del alumno enviada desde PHP
        const rutinas = <?= json_encode($rutina, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        const NOMBRES = {
            lunes: 'Lunes',
            martes: 'Martes',
            miercoles: 'Miércoles',
            jueves: 'Jueves',
            viernes: 'Viernes',
            sabado: 'Sábado',
            domingo: 'Domingo'
        };

        // Escape de texto (evita que se ejecute HTML escrito por el entrenador)
        function esc(t) {
            const d = document.createElement('div');
            d.textContent = t ?? '';
            return d.innerHTML;
        }

        const dias = document.querySelectorAll('.day-box');
        const detalle = document.getElementById('detalle');

        dias.forEach((dia) => {
            dia.addEventListener('click', () => {
                const key = dia.dataset.dia;
                const ejercicios = rutinas[key] || [];

                const nombresRutina = [...new Set(ejercicios.map(e => e.rutina))];
                let html = `<h3>Rutina del ${NOMBRES[key]}${nombresRutina.length ? ' — ' + esc(nombresRutina.join(' + ')) : ''}</h3>`;
                if (ejercicios.length === 0) {
                    html += '<p>Día de descanso</p>';
                } else {
                    html += ejercicios.map(e => `
                <p><strong>${esc(e.nombre)}</strong> — ${e.series} series de ${e.repeticiones} repeticiones —
                <strong>Carga:</strong> ${e.peso !== null ? e.peso + ' kg' : 'Peso corporal'}
                ${e.notas ? '<br><em>' + esc(e.notas) + '</em>' : ''}</p>`).join('');
                }

                detalle.innerHTML = html;
                detalle.classList.add('active');
            });
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

        // Descarga del PDF
        const openPdfModal = document.getElementById('open-pdf-modal');
        const pdfModal = document.getElementById('pdf-modal');
        const confirmPdf = document.getElementById('confirm-pdf');

        openPdfModal.addEventListener('click', () => {
            pdfModal.showModal();
        });

        confirmPdf.addEventListener('click', () => {
            window.location.href = 'descargar_rutina_pdf.php';
            pdfModal.close();
        });
    </script>
</body>

</html>