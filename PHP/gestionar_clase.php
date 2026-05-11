<?php
// --- CONTROLADOR DE ESTADOS DE CLASE ---
session_start();
include 'conexion.php'; // Conexión a la base de datos

//SEGURIDAD: Solo los profesores pueden gestionar sus clases
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../VIEWS/login.php");
    exit();
}

//RECOGIDA DE PARÁMETROS
//Obtenemos el ID de la clase y la acción (aceptar/rechazar) desde la URL (GET)
if (isset($_GET['id']) && isset($_GET['accion'])) {
    
    $clase_id = $conn->real_escape_string($_GET['id']);
    $accion = $_GET['accion'];
    $profesor_id = $_SESSION['usuario_id'];

    //Definimos el nuevo estado según la acción pulsada
    if ($accion === 'aceptar') {
        $nuevo_estado = 'aceptada';
    } elseif ($accion === 'rechazar') {
        $nuevo_estado = 'rechazada';
    } else {
        //Si la acción no es válida, devolvemos al panel
        header("Location: ../VIEWS/dashboard-profesor.php");
        exit();
    }

    //EJECUCIÓN DE LA ACTUALIZACIÓN
    //Añadimos una comprobación extra: el profesor_id debe coincidir con el del usuario logueado
    //Esto evita que un profesor acepte clases de otro profesor manipulando la URL
    $sql = "UPDATE clases 
            SET estado = '$nuevo_estado' 
            WHERE id = '$clase_id' AND profesor_id = '$profesor_id'";

    if ($conn->query($sql) === TRUE) {
        //Redirigimos con un mensaje de éxito (se puede leer con un $_GET['msg'] en el panel)
        header("Location: ../VIEWS/dashboard-profesor.php?status=success&msg=$nuevo_estado");
    } else {
        //Error de base de datos
        header("Location: ../VIEWS/dashboard-profesor.php?status=error");
    }

} else {
    //Si se accede al archivo sin parámetros
    header("Location: ../VIEWS/dashboard-profesor.php");
}

$conn->close();
?>