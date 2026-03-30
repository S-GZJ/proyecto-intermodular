<?php
session_start();
session_unset(); // Vaciamos las variables
session_destroy(); // Destruimos la sesión
header("Location: index.php"); // Redirigimos a la portada
exit();
?>