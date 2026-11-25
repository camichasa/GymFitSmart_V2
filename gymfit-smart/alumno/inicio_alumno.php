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

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Inicio
    </title>
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

    <!-- Carrusel -->
    <section class="carrusel" id="carrusel">
        <figure class="slides">
            <img class="slide active" src="../assets/img/carrusel/carrusel_1.jpg" alt="Imagen 1">
            <img class="slide" src="../assets/img/carrusel/carrusel_2.jpg" alt="Imagen 2">
            <img class="slide" src="../assets/img/carrusel/carrusel_3.jpg" alt="Imagen 3">
            <img class="slide" src="../assets/img/carrusel/carrusel_4.jpg" alt="imagen4">
        </figure>

        <button class="carrusel-btn prev" id="carrusel-prev" aria-label="Anterior">&#10094;</button>
        <button class="carrusel-btn next" id="carrusel-next" aria-label="Siguiente">&#10095;</button>

        <nav class="carrusel-puntos" id="carrusel-puntos" aria-label="Seleccionar imagen"></nav>
    </section>

    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel" id="cancel-logout">Cancelar</button>
        </form>
    </dialog>

    <footer>todos los derechos de Gymfit Smart estan reservados.</footer>

    <script>
        // Redirección al cerrar sesión
        const PAGINA_LOGIN = '../auth/cerrar_sesion.php';

        // Carrusel
        const slides = document.querySelectorAll('.slide');
        const contenedorPuntos = document.getElementById('carrusel-puntos');
        let indice = 0;
        let temporizador;

        slides.forEach((_, i) => {
            const punto = document.createElement('button');
            punto.className = 'punto' + (i === 0 ? ' active' : '');
            punto.addEventListener('click', () => {
                mostrarSlide(i);
                reiniciarAuto();
            });
            contenedorPuntos.appendChild(punto);
        });
        const puntos = document.querySelectorAll('.punto');

        function mostrarSlide(n) {
            slides[indice].classList.remove('active');
            puntos[indice].classList.remove('active');
            indice = (n + slides.length) % slides.length;
            slides[indice].classList.add('active');
            puntos[indice].classList.add('active');
        }

        function reiniciarAuto() {
            clearInterval(temporizador);
            temporizador = setInterval(() => mostrarSlide(indice + 1), 4000);
        }

        document.getElementById('carrusel-prev').addEventListener('click', () => {
            mostrarSlide(indice - 1);
            reiniciarAuto();
        });

        document.getElementById('carrusel-next').addEventListener('click', () => {
            mostrarSlide(indice + 1);
            reiniciarAuto();
        });

        reiniciarAuto();

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