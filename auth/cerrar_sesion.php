<?php

session_start();

session_destroy();

header("Location: iniciar_sesion.html");

exit();

?>