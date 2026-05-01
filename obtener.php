<?php
include "conexion.php";

// Solo mensajes entre usuario 1 y 2 (simulación chat)
$result = $conn->query("
  SELECT * FROM mensajes 
  WHERE (remitente_id = 1 AND destinatario_id = 2)
     OR (remitente_id = 2 AND destinatario_id = 1)
  ORDER BY fecha_envio ASC
");

$mensajes = [];

while ($row = $result->fetch_assoc()) {
    $mensajes[] = $row;
}

echo json_encode($mensajes);
?>