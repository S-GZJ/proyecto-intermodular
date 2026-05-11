<?php
//Iniciamos sesión para saber quién es el alumno
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexion.php';

//--ESCUDO DE SEGURIDAD--
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: ../VIEWS/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    
    //Limpiamos el número de tarjeta (quitamos espacios si el usuario los puso)
    $numero_completo = str_replace(' ', '', $_POST['numero']);
    
    //Validamos que sean 16 dígitos (básico)
    if (strlen($numero_completo) < 16) {
        header("Location: ../VIEWS/pagos-alumno.php?error=numero_invalido");
        exit();
    }

    //EXTRAEMOS SOLO LOS ÚLTIMOS 4 DÍGITOS (Seguridad)
    //Nunca guarda el número completo ni el CVV en tu base de datos si no eres un banco
    $ultimos_cuatro = substr($numero_completo, -4);
    
    //Detectamos el tipo de tarjeta (Si empieza por 4 es Visa, si empieza por 5 es Mastercard)
    $tipo = (substr($numero_completo, 0, 1) == '4') ? 'Visa' : 'Mastercard';

    //--VERIFICAR SI YA TIENE TARJETAS--
    //Si no tiene ninguna, esta será la 'predeterminada'
    $check_cards = $conn->query("SELECT id FROM metodos_pago WHERE usuario_id = '$usuario_id'");
    $es_primera = ($check_cards->num_rows == 0) ? 1 : 0;

    //--INSERTAR EN LA BASE DE DATOS--
    $sql = "INSERT INTO metodos_pago (usuario_id, tipo_tarjeta, ultimos_cuatro, predeterminada) 
            VALUES ('$usuario_id', '$tipo', '$ultimos_cuatro', '$es_primera')";

    if ($conn->query($sql)) {
        //Éxito: Volvemos a la página de pagos con un mensaje positivo
        header("Location: ../VIEWS/pagos-alumno.php?res=ok");
    } else {
        //Error de SQL
        header("Location: ../VIEWS/pagos-alumno.php?res=error");
    }
} else {
    //Si alguien intenta entrar directamente al archivo sin POST
    header("Location: ../VIEWS/dashboard-alumno.php");
}

$conn->close();
?>