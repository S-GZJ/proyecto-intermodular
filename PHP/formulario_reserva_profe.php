<?php
//--MOTOR DE RESERVAS ISIMatch--
session_start();
include 'conexion.php'; // Conexión a la base de datos

//--ESCUDO DE SEGURIDAD--
//Verificamos que el usuario sea un alumno y que la petición sea POST
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: ../VIEWS/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //--RECOGIDA Y SANEAMIENTO DE DATOS--
    //Usamos real_escape_string para evitar inyecciones SQL
    $alumno_id   = $_SESSION['usuario_id'];
    $profesor_id = $conn->real_escape_string($_POST['profesor_id']);
    $materia     = $conn->real_escape_string($_POST['materia']);
    $fecha_hora  = $conn->real_escape_string($_POST['fecha_hora']);
    $duracion    = (int)$_POST['duracion'];
    $precio_hora = (float)$_POST['precio_hora'];

    // Calculamos el precio total (Tarifa * (minutos/60))
    $precio_total = $precio_hora * ($duracion / 60);

    //--INSERCIÓN EN LA BASE DE DATOS--
    //El estado inicial siempre es 'pendiente' hasta que el profesor lo acepte
    $sql = "INSERT INTO clases (profesor_id, alumno_id, materia_nombre_manual, fecha_hora, duracion_minutos, precio_total, estado) 
            VALUES ('$profesor_id', '$alumno_id', '$materia', '$fecha_hora', '$duracion', '$precio_total', 'pendiente')";

    if ($conn->query($sql) === TRUE) {
        //Redirigimos al dashboard del alumno con un parámetro de éxito
        header("Location: ../VIEWS/dashboard-alumno.php?reserva=exitosa");
    } else {
        //Si hay error, volvemos atrás con un mensaje de error
        header("Location: ../VIEWS/ficha-profesor.php?id=$profesor_id&error=db");
    }
    
    $conn->close();
} else {
    //Si alguien intenta entrar directamente al archivo sin enviar el formulario
    header("Location: ../VIEWS/catalogo.php");
}
?>