<?php
/*--ARCHIVO DE CIERRE DE SESIÓN (logout.php)--
Este script elimina la conexión activa entre el usuario y el servidor
*/

//--REANUDAR SESIÓN--
//Para poder cerrar una sesión, primero debemos identificar cuál es la que está activa
session_start();

//--LIMPIEZA DE DATOS--
//session_unset() elimina todas las variables guardadas en la superglobal $_SESSION
//(como el nombre, el ID o el rol del usuario), pero mantiene la sesión viva
session_unset(); 

//--DESTRUCCIÓN TOTAL--
//session_destroy() elimina físicamente el archivo de sesión en el servidor y 
//rompe el vínculo definitivo con el navegador del usuario
session_destroy(); 

/*--REDIRECCIÓN Y FINALIZACIÓN--
Una vez que el usuario ya no está identificado, lo enviamos de vuelta 
a la página de inicio (index.php) para que vea la web como un visitante
*/
// RUTA CORREGIDA: Salimos de la carpeta PHP para llegar al index de la raíz
header("Location: ../index.php"); 

//exit() asegura que el script se detenga inmediatamente después de la redirección
exit();
?>