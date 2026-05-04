<?php
/**
 * ARCHIVO DE CONFIGURACIÓN DE LA BASE DE DATOS (conexion.php)
 * Este archivo establece el puente de comunicación entre PHP y MySQL.
 */

// 1. DEFINICIÓN DE PARÁMETROS DE CONEXIÓN
$servidor = "localhost";   // Dirección del servidor (en desarrollo local siempre es localhost)
$usuario = "root";        // Nombre del usuario administrador de la base de datos (por defecto en XAMPP)
$password = "";           // Contraseña del usuario (por defecto en XAMPP viene vacía)
$base_datos = "isimatch"; // Nombre exacto de la base de datos que creamos en phpMyAdmin

/**
 * 2. CREACIÓN DEL OBJETO DE CONEXIÓN
 * Utilizamos la extensión 'mysqli' (MySQL Improved), que es una de las formas 
 * oficiales y más eficientes de conectar PHP con bases de datos MySQL.
 * Se pasan los 4 parámetros definidos arriba en un orden específico.
 */
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

/**
 * 3. VALIDACIÓN DE LA CONEXIÓN
 * Es una buena práctica de programación verificar si la conexión tuvo éxito 
 * antes de intentar realizar cualquier consulta (SELECT, INSERT, etc.).
 */
if ($conn->connect_error) {
    /**
     * Si el objeto $conn tiene algún error de conexión:
     * - 'die' detiene inmediatamente la ejecución de la página.
     * - Mostramos un mensaje claro con el error técnico específico.
     */
    die("Error crítico: La conexión a la base de datos ha fallado: " . $conn->connect_error);
}

/** 
 * NOTA PARA LA PRESENTACIÓN: 
 * Si llegamos a este punto sin que se ejecute el 'die', significa que la 
 * conexión es exitosa y la variable $conn está lista para ser usada en 
 * el resto de las páginas del proyecto (como catalogo.php).
 */
?>