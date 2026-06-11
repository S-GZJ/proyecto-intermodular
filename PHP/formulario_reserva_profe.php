<?php
//--MOTOR DE RESERVAS ISIMatch--
session_start();
include 'conexion.php'; // Conexión a la base de datos

//--ESCUDO DE SEGURIDAD--
//Verificar que el usuario sea un alumno y que la petición sea POST
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: ../VIEWS/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alumno_id   = $_SESSION['usuario_id'];
    $profesor_id = (int)$_POST['profesor_id'];
    $materia     = $_POST['materia'];
    $fecha       = $_POST['fecha']; // YYYY-MM-DD
    $hora        = $_POST['hora'];  // HH:MM
    $duracion    = (int)$_POST['duracion'];
    $precio_hora = (float)$_POST['precio_hora'];

    //Juntar fecha y hora para el formato DATETIME de MySQL (YYYY-MM-DD HH:MM:00)
    $fecha_hora_final = $fecha . " " . $hora . ":00";
    $precio_total = $precio_hora * ($duracion / 60);

    //--VALIDACIÓN DE SEGURIDAD--
    //Comprobar de nuevo en el backend si esa hora exacta ya está pillada
    $sql_check = "SELECT id FROM clases 
                  WHERE profesor_id = ? 
                  AND fecha_hora = ? 
                  AND estado IN ('pendiente', 'aceptada')";
                  
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $profesor_id, $fecha_hora_final);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        //Alguien se adelantó o intentaron hackear el formulario
        header("Location: ../VIEWS/ficha-profesor.php?id=$profesor_id&error=hora_ocupada");
        exit();
    }
    //--FIN DE LA VALIDACIÓN--

    //Si está libre, se realiza la inserción segura con Prepared Statements
    $sql_insert = "INSERT INTO clases (profesor_id, alumno_id, materia_nombre_manual, fecha_hora, duracion_minutos, precio_total, estado) \n            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
                  
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iissid", $profesor_id, $alumno_id, $materia, $fecha_hora_final, $duracion, $precio_total);

    if ($stmt_insert->execute()) {
        header("Location: ../VIEWS/dashboard-alumno.php?reserva=exitosa");
    } else {
        header("Location: ../VIEWS/ficha-profesor.php?id=$profesor_id&error=db");
    }
    
    $stmt_check->close();
    $stmt_insert->close();
    $conn->close();
}