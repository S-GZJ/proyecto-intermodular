<?php
/*--ARCHIVO DE RECUPERACIÓN DE MENSAJES (obtener.php)--
Este script consulta la base de datos para extraer la conversación entre dos usuarios
*/

//--IMPORTACIÓN DE LA CONEXIÓN--
//Reutilizamos la conexión establecida en conexion.php para interactuar con la BDD
include "conexion.php";

/*--CONSULTA SQL CON LÓGICA DE CONVERSACIÓN--
Queremos obtener la historia del chat entre dos personas (en este caso, IDs 1 y 2)
Lógica de la cláusula WHERE:
(remitente 1 Y destinatario 2): Mensajes enviados por el usuario 1
(remitente 2 Y destinatario 1): Mensajes enviados por el usuario 2 (las respuestas)
ORDER BY fecha_envio ASC:
Asegura que los mensajes aparezcan en orden cronológico (del más antiguo al más reciente)
*/
$result = $conn->query("
  SELECT * FROM mensajes 
  WHERE (remitente_id = 1 AND destinatario_id = 2)
     OR (remitente_id = 2 AND destinatario_id = 1)
  ORDER BY fecha_envio ASC
");

//--ESTRUCTURACIÓN DE DATOS--
//Creamos un array vacío para almacenar todos los mensajes que encontremos
$mensajes = [];

/*--BUCLE DE RECORRIDO (Fetch)--
El método fetch_assoc() recorre fila por fila los resultados de la consulta
Cada fila (mensaje) se añade como un nuevo elemento al array $mensajes
 */
while ($row = $result->fetch_assoc()) {
    $mensajes[] = $row;
}

/*--SALIDA EN FORMATO JSON--
Transformamos el array de PHP en un objeto JSON (JavaScript Object Notation)
¿Por qué JSON?
Porque es el estándar universal para enviar datos entre un servidor (PHP) 
y una aplicación cliente (JavaScript/AJAX) de forma ligera y fácil de procesar
*/
echo json_encode($mensajes);
?>