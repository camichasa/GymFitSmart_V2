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

$id = $_GET["id"];

require("../../includes/dias_semana.php");

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


    $sql = "SELECT *
            FROM rutinas
            WHERE id = :id
            AND entrenador_id = :entrenador_id";

    $resultado = $base->prepare($sql);

    $resultado->execute(
        array(
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );

    $rutina = $resultado->fetch(PDO::FETCH_ASSOC);


    if (!$rutina) {

        echo "Rutina no encontrada.";
        exit();
    }


    $sqlAlumnos = "SELECT id, nombre, email
                   FROM usuarios
                   WHERE rol = 'alumno'";

    $resultadoAlumnos = $base->prepare($sqlAlumnos);

    $resultadoAlumnos->execute();

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

    <title>Editar Rutina - GymFit Smart</title>

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

        <a href="listado_rutinas.php">
            Rutinas
        </a>

        <a href="../ejercicios/listado_ejercicios.php">
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
                Editar Rutina
            </h2>

            <form
                action="actualizar_rutina.php"
                method="post"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php
                    echo $rutina["id"];
                    ?>"
                >


                <label for="nombre">
                    Nombre de la rutina
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="<?php
                    echo htmlspecialchars(
                        $rutina["nombre"]
                    );
                    ?>"
                    required
                >

                <br><br>


                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    required
                ><?php
                echo htmlspecialchars(
                    $rutina["descripcion"]
                );
                ?></textarea>

                <br><br>


                <label for="alumno">
                    Alumno
                </label>

                <select
                    name="alumno_id"
                    id="alumno"
                    required
                >

                    <?php

                    while (
                        $alumno =
                        $resultadoAlumnos->fetch(
                            PDO::FETCH_ASSOC
                        )
                    ) {

                    ?>

                        <option
                            value="<?php
                            echo $alumno["id"];
                            ?>"

                            <?php

                            if (
                                $alumno["id"] ==
                                $rutina["alumno_id"]
                            ) {

                                echo "selected";
                            }

                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $alumno["nombre"]
                            );
                            ?>

                            -

                            <?php
                            echo htmlspecialchars(
                                $alumno["email"]
                            );
                            ?>

                        </option>

                    <?php

                    }

                    ?>

                </select>

                <br><br>


                <label for="dia">
                    Día de entrenamiento
                </label>

                <select
                    name="dia"
                    id="dia"
                    required
                >

                    <option value="">
                        Seleccionar día
                    </option>

                    <?php

                    foreach (diasSemana() as $valor => $texto) {

                    ?>

                        <option
                            value="<?php
                            echo $valor;
                            ?>"

                            <?php

                            if ($rutina["dia"] == $valor) {

                                echo "selected";
                            }

                            ?>
                        >
                            <?php
                            echo $texto;
                            ?>
                        </option>

                    <?php

                    }

                    ?>

                </select>

                <br><br>


                <input
                    type="submit"
                    value="GUARDAR CAMBIOS"
                >

            </form>

            <br>

            <a
                href="listado_rutinas.php"
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

$resultadoAlumnos->closeCursor();

$base = null;

?>