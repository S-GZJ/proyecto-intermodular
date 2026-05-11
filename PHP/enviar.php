<?php
/*ARCHIVO DE PROCESAMIENTO DE MENSAJES (enviar.php) --
Este script recibe los datos del formulario de chat y los guarda en la base de datos
*/

//IMPORTACIÓN DE LA CONEXIÓN reutilizamos el objeto $conn para interactuar con MySQL
include "conexion.php";

//--RECEPCIÓN DE DATOS--
// extraemos el texto enviado por el usuario a través del método POST 
// (normalmente desde un campo llamado 'mensaje').
$contenido = $_POST['mensaje'];

/*--IDENTIFICACIÓN DE USUARIOS--
Por ahora, estos IDs están fijos (hardcoded) para pruebas.
En la versión final, el $remitente_id se obtiene de $_SESSION['usuario_id']
*/
$remitente_id = 1;     //El que envía el mensaje
$destinatario_id = 2;  //El que recibe el mensaje

/*--PREPARACIÓN DE LA CONSULTA SQL (Seguridad)--
Utilizamos "Prepared Statements" (Consultas Preparadas) para evitar la Inyección SQL
Los signos de interrogación (?) actúan como marcadores de posición
 */
$sql = "INSERT INTO mensajes (remitente_id, destinatario_id, contenido) VALUES (?, ?, ?)";

//Preparamos la consulta en el servidor
$stmt = $conn->prepare($sql);

/*--VINCULACIÓN DE PARÁMETROS (Bind Param)--
"iis" indica el tipo de datos de los marcadores (?):
i = integer (entero) para los IDs
s = string (cadena de texto) para el contenido del mensaje
*/
$stmt->bind_param("iis", $remitente_id, $destinatario_id, $contenido);

//EJECUCIÓN
//Se ejecuta la instrucción y se inserta el nuevo registro en la tabla 'mensajes'
$stmt->execute();

/*--RESPUESTA AL CLIENTE--
Enviamos un "ok" para que el código JavaScript del frontend (AJAX), 
sepa que el mensaje se guardó correctamente y pueda limpiar el cuadro de texto.
 */
echo "ok";
?>