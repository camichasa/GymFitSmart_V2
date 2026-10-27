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

    $sql = "SELECT id, nombre
            FROM rutinas
            WHERE entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );

} catch (Exception $e) {

    echo "Error: " . $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/estilos_entrenador.css"
    >

    <title>Nuevo Ejercicio - GymFit Smart</title>

</head>

<body>

    <header>

        <h1 class="logo-texto">
            GYMFIT SMART
        </h1>

        <p>
            Entrenador:
            <?php
            echo htmlspecialchars(
                $_SESSION["nombre"]
            );
            ?>
        </p>

    </header>


    <nav>

        <a href="../panel_entrenador.php">
            Inicio
        </a>

        <a href="../alumnos/listado_alumnos.php">
            Alumnos
        </a>
        
        <a href="../rutinas/listado_rutinas.php">
            Rutinas
        </a>

        <a href="listado_ejercicios.php">
            Ejercicios
        </a>

        <a href="../reportes/reporte_rutinas.php">
            Reportes
        </a>

        <a href="#" id="logout-link">
            Cerrar sesión
        </a>

    </nav>


    <main class="contenedor">

        <section class="tarjeta">

            <h2>
                Nuevo Ejercicio
            </h2>

            <form
                action="guardar_ejercicio.php"
                method="post"
            >

                <label for="nombre">
                    Nombre del ejercicio
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    required
                >

                <br><br>


                <label for="rutina">
                    Rutina
                </label>

                <select
                    name="rutina_id"
                    id="rutina"
                    required
                >

                    <option value="">
                        Seleccionar rutina
                    </option>

                    <?php

                    while (
                        $rutina =
                        $resultado->fetch(
                            PDO::FETCH_ASSOC
                        )
                    ) {

                    ?>

                        <option
                            value="<?php
                            echo $rutina["id"];
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $rutina["nombre"]
                            );
                            ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

                <br><br>


                <label for="series">
                    Series
                </label>

                <input
                    type="number"
                    name="series"
                    id="series"
                    required
                >

                <br><br>


                <label for="repeticiones">
                    Repeticiones
                </label>

                <input
                    type="number"
                    name="repeticiones"
                    id="repeticiones"
                    required
                >

                <br><br>


                <label for="peso">
                    Peso sugerido
                </label>

                <input
                    type="number"
                    name="peso"
                    id="peso"
                    step="0.01"
                >

                <br><br>


                <label for="orden">
                    Orden dentro de la rutina
                </label>

                <input
                    type="number"
                    name="orden"
                    id="orden"
                    min="1"
                    value="1"
                    required
                >

                <br><br>


                <label for="notas">
                    Notas
                </label>

                <input
                    type="text"
                    name="notas"
                    id="notas"
                >

                <br><br>


                <input
                    type="submit"
                    value="GUARDAR EJERCICIO"
                >

            </form>

            <br>

            <a
                href="listado_ejercicios.php"
                class="btn-link"
            >
                VOLVER
            </a>

        </section>

    </main>


    <footer>

        <p>
            GymFit Smart
        </p>

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

<?php

$resultado->closeCursor();

$base = null;

?>