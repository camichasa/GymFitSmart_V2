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

    $sql = "SELECT rutinas.id,
                   rutinas.nombre,
                   rutinas.descripcion,
                   rutinas.dia,
                   rutinas.fecha_creacion,
                   usuarios.nombre AS nombre_alumno
            FROM rutinas
            INNER JOIN usuarios
            ON rutinas.alumno_id = usuarios.id
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
    </meta>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    </meta>

    <link
        rel="stylesheet"
        href="../../assets/css/estilos_entrenador.css">
    </link>

    <title>Rutinas - GymFit Smart</title>

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


    <main>

        <section>

            <h2>
                Rutinas
            </h2>

            <p>
                Desde aquí podés administrar
                las rutinas de tus alumnos.
            </p>

            <br>

            <a
                href="formulario_nueva_rutina.php"
                class="btn-link">
                NUEVA RUTINA
            </a>

            <br><br>

            <table border="1">

                <tr>

                    <th>ID</th>

                    <th>Nombre</th>

                    <th>Descripción</th>

                    <th>Alumno</th>

                    <th>Día</th>

                    <th>Fecha</th>

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
                                $registro["descripcion"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["nombre_alumno"]
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                nombreDia($registro["dia"] ?? "")
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registro["fecha_creacion"]
                            );
                            ?>
                        </td>

                        <td>

                            <a
                                href="ver_ejercicios_rutina.php?id=<?php
                                                                    echo $registro["id"];
                                                                    ?>">
                                Ver ejercicios
                            </a>

                            |

                            <a
                                href="formulario_editar_rutina.php?id=<?php
                                                            echo $registro["id"];
                                                            ?>">
                                Modificar
                            </a>

                            |

                            <a
                                href="eliminar_rutina.php?id=<?php
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