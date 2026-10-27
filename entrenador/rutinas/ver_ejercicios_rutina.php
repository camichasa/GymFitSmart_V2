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


    $sqlRutina = "SELECT rutinas.id,
                         rutinas.nombre,
                         rutinas.descripcion,
                         rutinas.dia,
                         usuarios.nombre AS nombre_alumno
                  FROM rutinas
                  INNER JOIN usuarios
                  ON rutinas.alumno_id = usuarios.id
                  WHERE rutinas.id = :id
                  AND rutinas.entrenador_id = :entrenador_id";

    $resultadoRutina = $base->prepare($sqlRutina);

    $resultadoRutina->execute(
        array(
            ":id" => $id,
            ":entrenador_id" => $_SESSION["id_usuario"]
        )
    );

    $rutina = $resultadoRutina->fetch(PDO::FETCH_ASSOC);


    if (!$rutina) {

        echo "Rutina no encontrada.";
        exit();
    }


    $sqlEjercicios = "SELECT *
                      FROM ejercicios
                      WHERE rutina_id = :rutina_id";

    $resultadoEjercicios = $base->prepare($sqlEjercicios);

    $resultadoEjercicios->execute(
        array(
            ":rutina_id" => $id
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

    <title>Ejercicios de la Rutina - GymFit Smart</title>

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
        /a>

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


    <main>

        <section>

            <h2>
                Rutina:
                <?php
                echo htmlspecialchars(
                    $rutina["nombre"]
                );
                ?>
            </h2>

            <p>
                Alumno:
                <?php
                echo htmlspecialchars(
                    $rutina["nombre_alumno"]
                );
                ?>
            </p>

            <p>
                Día de entrenamiento:
                <?php
                echo htmlspecialchars(
                    nombreDia($rutina["dia"] ?? "")
                );
                ?>
            </p>

            <p>
                Descripción:
                <?php
                echo htmlspecialchars(
                    $rutina["descripcion"]
                );
                ?>
            </p>

            <br>

            <h3>
                Ejercicios asignados
            </h3>

            <table border="1">

                <tr>

                    <th>ID</th>

                    <th>Ejercicio</th>

                    <th>Series</th>

                    <th>Repeticiones</th>

                    <th>Peso sugerido</th>

                    <th>Orden</th>

                    <th>Notas</th>

                </tr>

                <?php

                while (
                    $ejercicio =
                    $resultadoEjercicios->fetch(
                        PDO::FETCH_ASSOC
                    )
                ) {

                ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["id"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["nombre"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["series"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["repeticiones"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["peso_sugerido"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["orden"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $ejercicio["notas"] ?? ""
                            );
                            ?>
                        </td>

                    </tr>

                <?php

                }

                ?>

            </table>

            <br><br>

            <a
                href="listado_rutinas.php"
                class="btn-link"
            >
                VOLVER A RUTINAS
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

$resultadoRutina->closeCursor();

$resultadoEjercicios->closeCursor();

$base = null;

?>