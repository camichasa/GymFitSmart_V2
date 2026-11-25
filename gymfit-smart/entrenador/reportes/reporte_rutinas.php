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

    $sql = "SELECT rutinas.nombre,
                   rutinas.descripcion,
                   rutinas.fecha_creacion,
                   usuarios.nombre AS nombre_alumno,
                   usuarios.email AS email_alumno
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <link
        rel="stylesheet"
        href="../../assets/css/estilos_entrenador.css">

    <title>Reportes - GymFit Smart</title>

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

        <a href="../ejercicios/listado_ejercicios.php">
            Ejercicios
        </a>

        <a href="reporte_rutinas.php">
            Reportes
        </a>

        <a href="#" id="logout-link">
            Cerrar sesión
        </a>

    </nav>


    <main>

        <section>

            <h2>
                Reportes
            </h2>

            <p>
                Información de las rutinas y alumnos
                asociados al entrenador.
            </p>

            <br>

            <a
                href="exportar_reporte_csv.php"
                class="btn-link">
                DESCARGAR EXCEL
            </a>

            <br><br>


            <table border="1">

                <tr>

                    <th>Rutina</th>

                    <th>Alumno</th>

                    <th>Email</th>

                    <th>Descripción</th>

                    <th>Fecha</th>

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
                                $registro["nombre"]
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
                                $registro["email_alumno"]
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
                                $registro["fecha_creacion"]
                            );
                            ?>

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