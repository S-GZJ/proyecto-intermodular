<?php
include 'conexion.php';

$profesor_id = isset($_GET['profesor_id']) ? (int)$_GET['profesor_id'] : 0;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
// Recibir la duración que quiere el alumno actual (por defecto 60 min)
$duracion_alumno = isset($_GET['duracion']) ? (int)$_GET['duracion'] : 60; 

$horas_ocupadas = [];

if ($profesor_id > 0 && !empty($fecha)) {
    
    // Traer las clases existentes en ese día
    $sql = "SELECT TIME(fecha_hora) AS hora_inicio, duracion_minutos FROM clases 
            WHERE profesor_id = ? 
            AND DATE(fecha_hora) = ? 
            AND estado IN ('pendiente', 'aceptada')";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $profesor_id, $fecha);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Almacenar los rangos de las clases existentes para cruzarlos luego
    $clases_existentes = [];
    while ($fila = $resultado->fetch_assoc()) {
        $inicio = substr($fila['hora_inicio'], 0, 5);
        $duracion = (int)$fila['duracion_minutos'];
        
        $parts = explode(':', $inicio);
        $inicio_min = ($parts[0] * 60) + $parts[1];
        $fin_min = $inicio_min + $duracion;
        
        $clases_existentes[] = ['inicio' => $inicio_min, 'fin' => $fin_min];
    }

    // Lista de horas estáticas del sistema
    $horas_posibles_sistema = ["09:00", "10:00", "11:00", "12:00", "13:00", "16:00", "17:00", "18:00", "19:00", "20:00"];

    // Evaluar cada hora del sistema
    foreach ($horas_posibles_sistema as $hora_bloque) {
        $parts_bloque = explode(':', $hora_bloque);
        $bloque_inicio_min = ($parts_bloque[0] * 60) + $parts_bloque[1];
        // Calcular cuándo terminaría la clase del alumno si la pide a esta hora
        $bloque_fin_min = $bloque_inicio_min + $duracion_alumno;

        // Comprobar si este rango que quiere el alumno colisiona con otra clase ya reservada
        foreach ($clases_existentes as $clase) {
            // Hay solapamiento si el bloque del alumno empieza antes de que termine la clase existente y termina después de que empiece la clase existente.
            if ($bloque_inicio_min < $clase['fin'] && $bloque_fin_min > $clase['inicio']) {
                if (!in_array($hora_bloque, $horas_ocupadas)) {
                    $horas_ocupadas[] = $hora_bloque;
                }
                break;
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($horas_ocupadas);