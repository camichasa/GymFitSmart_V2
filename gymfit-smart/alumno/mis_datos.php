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
        Mis datos
    </title>
    <link rel="stylesheet" href="../assets/css/estilos_alumno.css?v=2">
</head>

<body>

    <header>

        <button class="hamburguesa" id="btn-menu" aria-label="Abrir menú" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <a class="logo" href="inicio_alumno.php"> GymFit Smart</a>
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

    <form class="contenedor" action="guardar_datos.php" method="POST">
        <section class="datos-box">
            <h2>Mis datos</h2>

            <label>Peso (kg)
                <input type="number" name="peso" min="20" max="300" step="0.1" required>
            </label>

            <label>Altura (cm)
                <input type="number" name="altura" min="100" max="250" required>
            </label>

            <label>Edad
                <input type="number" name="edad" min="10" max="100" required>
            </label>

            <label>Sexo
                <select name="sexo" required>
                    <option value="">Seleccionar</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">otro</option>
                </select>
            </label>
        </section>


        <section class="objetivo-box">
            <h3>Mis objetivos y experiencia</h3>

            <label>Objetivo
                <select name="objetivo" required>
                    <option value="">Elige una opción</option>
                    <option value="Bajar peso">Bajar peso</option>
                    <option value="Ganar musculo">Ganar musculo</option>
                    <option value="Mejorar mi salud">Mejorar mi salud</option>
                </select>
            </label>

            <label>Experiencia
                <select name="experiencia">
                    <option value="">Elige una opción</option>
                    <option value="Ninguna">Ninguna</option>
                    <option value="Poca">Poca</option>
                    <option value="Media">Media</option>
                    <option value="Mucha">Mucha</option>
                </select>
            </label>

            <label>Dias de entrenamiento
                <select name="Dias_entrenamiento">
                    <option value="">Elige una opcion</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                </select>
            </label>
        </section>

        <section class="emergency-box">
            <h4>Contacto de emergencia</h4>

            <label>Nombre y Apellido
                <input type="text" name="Nombre" min="5" max="30">
            </label>

            <label>Numero de telefono
                <input type="tel" name="Numero" placeholder="Ej: 11 3333 4444" required>
            </label>
        </section>
        <button type="submit" class="btn-guardar">Guardar mis datos</button>

    </form>

    <dialog id="logout-modal">
        <p>¿Está seguro que quiere cerrar sesión?</p>
        <form method="dialog" class="modal-buttons">
            <button class="btn btn-confirm" type="button" id="confirm-logout">Sí, cerrar</button>
            <button class="btn btn-cancel">Cancelar</button>
        </form>
    </dialog>

    <footer>todos los derechos de Gymfit Smart estan reservados.</footer>

    <script>
        // Redirección al cerrar sesión
        const PAGINA_LOGIN = '../auth/cerrar_sesion.php';

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