<?php
//Configuración para entorno LOCAL (XAMPP)
$host = "localhost"; 
$user = "root";           
$db   = "isimatch";
$pass = ""; //En localhost, por defecto la contraseña suele estar vacía

$conn = new mysqli($host, $user, $pass, $db);

//Configuración de caracteres para evitar problemas con tildes y ñ
$conn->set_charset("utf8mb4");

// Verificar si hay errores
if ($conn->connect_error) {
    die("Error de conexión local: " . $conn->connect_error);
}

?>