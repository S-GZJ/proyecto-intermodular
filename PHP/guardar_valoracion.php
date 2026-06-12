<?php
/**
 * ISIMatch - Controlador: Guardar Valoración
 * Recibe el POST del formulario valorar.php, valida todos los datos
 * e inserta la reseña en la BD. Actualiza el promedio del profesor.
 */
session_start();
include 'conexion.php';

/*-- ESCUDO DE SEGURIDAD --*/
// Solo alumnos autenticados pueden enviar valoraciones
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: ../VIEWS/login.php");
    exit();
}

// Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../VIEWS/dashboard-alumno.php");
    exit();
}

$alumno_id = $_SESSION['usuario_id'];

/*-- RECOGIDA Y SANEAMIENTO DE DATOS --*/
$clase_id    = isset($_POST['clase_id'])    ? (int) $_POST['clase_id']    : 0;
$profesor_id = isset($_POST['profesor_id']) ? (int) $_POST['profesor_id'] : 0;
$puntuacion  = isset($_POST['puntuacion'])  ? (int) $_POST['puntuacion']  : 0;
$comentario  = isset($_POST['comentario'])  ? trim($_POST['comentario'])   : '';

// Sanitización del comentario (máx. 500 caracteres)
$comentario = mb_substr($comentario, 0, 500);


/*-- VALIDACIÓN 1: IDs válidos --*/
if ($clase_id <= 0 || $profesor_id <= 0) {
    header("Location: ../VIEWS/dashboard-alumno.php");
    exit();
}

/*-- VALIDACIÓN 2: Puntuación en rango 1-5 --*/
if ($puntuacion < 1 || $puntuacion > 5) {
    header("Location: ../VIEWS/valorar.php?id=$clase_id&error=puntuacion");
    exit();
}

/*-- VALIDACIÓN 3: La clase pertenece al alumno y está aceptada --*/
// Solo se permiten valoraciones de clases aceptadas.
$stmt = $conn->prepare("
    SELECT id FROM clases
    WHERE id = ? AND alumno_id = ? AND profesor_id = ? AND estado = 'aceptada'
    AND fecha_hora < NOW()
");
$stmt->bind_param("iii", $clase_id, $alumno_id, $profesor_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: ../VIEWS/valorar.php?id=$clase_id&error=permisos");
    exit();
}
$stmt->close();

/*-- VALIDACIÓN 4: Evitar valoraciones duplicadas --*/
// Cada alumno solo puede valorar una vez cada clase.
$stmt2 = $conn->prepare("SELECT id FROM resenas WHERE clase_id = ? AND alumno_id = ?");
$stmt2->bind_param("ii", $clase_id, $alumno_id);
$stmt2->execute();
$stmt2->store_result();

if ($stmt2->num_rows > 0) {
    $stmt2->close();
    header("Location: ../VIEWS/valorar.php?id=$clase_id&error=duplicada");
    exit();
}
$stmt2->close();

/*-- GUARDAR LA NUEVA RESEÑA --*/
$stmt3 = $conn->prepare("
    INSERT INTO resenas (clase_id, profesor_id, alumno_id, puntuacion, comentario)
    VALUES (?, ?, ?, ?, ?)
");
$stmt3->bind_param("iiiis", $clase_id, $profesor_id, $alumno_id, $puntuacion, $comentario);

if (!$stmt3->execute()) {
    $stmt3->close();
    header("Location: ../VIEWS/valorar.php?id=$clase_id&error=bd");
    exit();
}
$stmt3->close();

/*-- ACTUALIZAR PROMEDIO Y TOTAL EN profesores_detalles --*/
// Recalculamos la media real a partir de todas las reseñas del profesor
$stmt4 = $conn->prepare("
    SELECT COUNT(*) AS total, ROUND(AVG(puntuacion), 2) AS media
    FROM resenas
    WHERE profesor_id = ?
");
$stmt4->bind_param("i", $profesor_id);
$stmt4->execute();
$res = $stmt4->get_result()->fetch_assoc();
$stmt4->close();

$nueva_media = $res['media'];
$nuevo_total = $res['total'];

$stmt5 = $conn->prepare("
    UPDATE profesores_detalles
    SET valoracion_media = ?, total_resenas = ?
    WHERE usuario_id = ?
");
$stmt5->bind_param("dii", $nueva_media, $nuevo_total, $profesor_id);
$stmt5->execute();
$stmt5->close();

$conn->close();

/*-- REDIRECCIÓN FINAL CON ÉXITO --*/
header("Location: ../VIEWS/ficha-profesor.php?id=$profesor_id&status=valoracion_ok");
exit();
?>
