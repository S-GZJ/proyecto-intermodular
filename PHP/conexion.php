<?php
/*ARCHIVO DE CONFIGURACIÓN DE LA BASE DE DATOS (conexion.php), 
este archivo establece el puente de comunicación entre PHP y MySQL*/

//--DEFINICIÓN DE PARÁMETROS DE CONEXIÓN--
$servidor = "localhost";   //Dirección del servidor (en este caso, es localhost ya que está en local)
$usuario = "root";        //Nombre del usuario admini de la base de datos (por defecto en XAMPP)
$password = "";           //Contraseña del usuario (por defecto en XAMPP, viene vacía)
$base_datos = "isimatch"; //Nombre exacto de la bdd que creamos en phpMyAdmin

/*--CREACIÓN DEL OBJETO DE CONEXIÓN--
Utilizamos la extensión 'mysqli' (MySQL Improved), que es una de las formas 
oficiales y más eficientes de conectar PHP con bases de datos MySQL
Se pasan los 4 parámetros definidos arriba en un orden específico*/
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

/*--VALIDACIÓN DE LA CONEXIÓN--
Es una buena práctica de programación verificar si la conexión tuvo éxit,
antes de intentar realizar cualquier consulta (SELECT, INSERT...)
*/
if ($conn->connect_error) {
    /*Si el objeto $conn tiene algún error de conexión:
    - 'die' detiene inmediatamente la ejecución de la página.
    - Mostramos un mensaje claro con el error técnico específico.
     */
    die("Error crítico: La conexión a la base de datos ha fallado: " . $conn->connect_error);
}

/*--NOTA ADICIONAL-- 
Si llegamos a este punto sin que se ejecute el 'die', significa que la 
conexión es exitosa y la variable $conn está lista para ser usada en 
el resto de las páginas del proyecto (como catalogo.php).
*/
?>