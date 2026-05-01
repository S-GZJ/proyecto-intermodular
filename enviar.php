<?php
include "conexion.php";

$contenido = $_POST['mensaje'];

// ⚠️ Esto luego vendrá del login
$remitente_id = 1;
$destinatario_id = 2;

$sql = "INSERT INTO mensajes (remitente_id, destinatario_id, contenido) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $remitente_id, $destinatario_id, $contenido);
$stmt->execute();

echo "ok";
?>