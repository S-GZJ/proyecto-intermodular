<?php
$servidor = "localhost";
$usuario = "root"; // Usuario por defecto en XAMPP
$password = "";    // Contraseña por defecto en XAMPP (vacía)
$base_datos = "isimatch";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Comprobar conexión
if ($conn->connect_error) {
    die("La conexión ha fallado: " . $conn->connect_error);
}
?>