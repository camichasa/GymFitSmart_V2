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

    $sql = "SELECT ejercicios.id,
                   ejercicios.nombre,
                   ejercicios.series,
                   ejercicios.repeticiones,
                   ejercicios.peso_sugerido,
                   rutinas.dia AS dia_rutina,
                   ejercicios.orden,
                   ejercicios.notas,
                   rutinas.nombre AS nombre_rutina
            FROM ejercicios
            INNER JOIN rutinas
            ON ejercicios.rutina_id = rutinas.id
            WHERE rutinas.entrenador_id = :entrenador_id";

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
        content="width=device-width, initial-scale=1.0">
    </meta>
    <link
        rel="stylesheet"
        href="../../assets/css/estilos_entrenador.css">
    </link>
    <title>Ejercicios - GymFit Smart</title>

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


    <main>

        <section>

            <h2>
                Ejercicios
            </h2>

            <p>
                Desde aquí podés administrar
                los ejercicios de tus rutinas.
            </p>

            <br>

            <a
                href="formulario_nuevo_ejercicio.php"
                class="btn-link">
                NUEVO EJERCICIO
            </a>

            <br><br>

            <table border="1">

                <tr>

                    <th>ID</th>

                    <th>Nombre</th>

                    <th>Rutina</th>

                    <th>Series</th>

                    <th>Repeticiones</th>

                    <th>Peso sugerido</th>

                    <th>Día de la rutina</th>

                    <th>Orden</th>

                    <th>Notas</th>

                    <th>Acciones</th>

                </tr>

                <?php

                while (
                    $registro =
                    $resultado->fetch(
                        PDO::FETCH_ASSOC
                    )
                ) {

                ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["id"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["nombre"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["nombre_rutina"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["series"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["repeticiones"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["peso_sugerido"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                nombreDia($registro["dia_rutina"] ?? "")
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["orden"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["notas"] ?? ""
                            );
                            ?>
                        </td>

                        <td>

                            <a
                                href="formulario_editar_ejercicio.php?id=<?php
                                                                echo $registro["id"];
                                                                ?>">
                                Modificar
                            </a>

                            |

                            <a
                                href="eliminar_ejercicio.php?id=<?php
                                                                echo $registro["id"];
                                                                ?>">
                                Eliminar
                            </a>

                        </td>

                    </tr>

                <?php

                }

                ?>

            </table>

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

$base = null;

?>