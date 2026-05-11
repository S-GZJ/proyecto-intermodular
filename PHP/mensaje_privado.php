<?php
// ---PROCESADOR DE MENSAJERÍA PRIVADA---
session_start();
include 'conexion.php';

//SEGURIDAD
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../VIEWS/login.php");
    exit();
}

//RECOGIDA DE DATOS
$remitente_id = $_SESSION['usuario_id'];
$destinatario_id = $conn->real_escape_string($_POST['destinatario_id']);
$contenido = $conn->real_escape_string($_POST['contenido']);

//VALIDACIÓN MÍNIMA
if (empty($contenido)) {
    header("Location: ../VIEWS/mensajes.php?con=$destinatario_id&error=vacio");
    exit();
}

//INSERCIÓN EN BASE DE DATOS
$sql = "INSERT INTO mensajes (remitente_id, destinatario_id, contenido, leido) 
        VALUES ('$remitente_id', '$destinatario_id', '$contenido', 0)";

if ($conn->query($sql) === TRUE) {
    //Éxito: Volvemos a la conversación
    header("Location: ../VIEWS/mensajes.php?con=$destinatario_id");
} else {
    //Error
    header("Location: ../VIEWS/mensajes.php?con=$destinatario_id&error=db");
}

$conn->close();
?>