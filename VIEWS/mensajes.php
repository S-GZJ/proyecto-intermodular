<?php
/**
 * ISIMatch - Sistema de Mensajería Privada
 * Gestiona la lista de contactos, el historial de chat y la lectura de mensajes.
 */

//--LÓGICA DE SERVIDOR (PHP)--

//Iniciamos o reanudamos la sesión del usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../PHP/conexion.php';

//--ESCUDO DE SEGURIDAD: Si no hay ID de usuario, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

//Variables de identidad del usuario actual
$mi_id = $_SESSION['usuario_id'];
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
$inicial_mia = strtoupper(substr($nombre_usuario, 0, 1)); //Inicial para el menú superior

//--OBTENER LISTA DE CONVERSACIONES (SQL AVANZADO)--
/*Esta consulta busca a todas las personas con las que el usuario ha hablado.
 -Usa una subconsulta para traer el último mensaje enviado o recibido.
 -Cuenta cuántos mensajes hay sin leer de ese contacto específico.
 -Filtra la tabla usuarios para mostrar solo a quienes están vinculados por mensajes.
 */
$sql_contactos = "SELECT 
                    u.id AS contacto_id, 
                    u.nombre, 
                    u.apellidos, 
                    u.rol,
                    (SELECT contenido FROM mensajes 
                     WHERE (remitente_id = u.id AND destinatario_id = '$mi_id') 
                        OR (remitente_id = '$mi_id' AND destinatario_id = u.id) 
                     ORDER BY fecha_envio DESC LIMIT 1) AS ultimo_msj,
                    (SELECT COUNT(*) FROM mensajes 
                     WHERE remitente_id = u.id AND destinatario_id = '$mi_id' AND leido = 0) AS no_leidos
                  FROM usuarios u
                  WHERE u.id IN (
                      SELECT DISTINCT CASE WHEN remitente_id = '$mi_id' THEN destinatario_id ELSE remitente_id END
                      FROM mensajes WHERE remitente_id = '$mi_id' OR destinatario_id = '$mi_id'
                  )";

$res_contactos = $conn->query($sql_contactos);

//--GESTIÓN DEL CHAT SELECCIONADO--
//Detectamos si el usuario ha pulsado sobre un contacto específico (?con=ID)
$chat_con = isset($_GET['con']) ? $conn->real_escape_string($_GET['con']) : null;
$mensajes_chat = [];

if ($chat_con) {
    //MARCAR COMO LEÍDOS: Al abrir el chat, actualizamos los mensajes recibidos a leido = 1
    $conn->query("UPDATE mensajes SET leido = 1 WHERE remitente_id = '$chat_con' AND destinatario_id = '$mi_id'");

    //CARGAR HISTORIAL: Traemos todos los mensajes entre el usuario actual y el contacto seleccionado
    $sql_chat = "SELECT * FROM mensajes 
                 WHERE (remitente_id = '$mi_id' AND destinatario_id = '$chat_con')
                    OR (remitente_id = '$chat_con' AND destinatario_id = '$mi_id')
                 ORDER BY fecha_envio ASC";
    $res_chat = $conn->query($sql_chat);
    
    //Guardamos los mensajes en un array para recorrerlos después en el HTML
    while($row = $res_chat->fetch_assoc()){
        $mensajes_chat[] = $row;
    }
    
    //INFO DEL CONTACTO: Obtenemos el nombre y rol de la persona con la que hablamos
    $info_contacto = $conn->query("SELECT nombre, apellidos, rol FROM usuarios WHERE id = '$chat_con'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        /*Estilos específicos para la interfaz de chat*/
        body { background-color: #f0f2f5; height: 100vh; display: flex; flex-direction: column; }
        .chat-container { height: calc(100vh - 120px); background: white; border-radius: 15px; overflow: hidden; }
        .contacts-column { border-right: 1px solid #f0f2f5; overflow-y: auto; }
        .messages-area { flex-grow: 1; overflow-y: auto; padding: 25px; background-color: #f9f9f9; }
        
        /*Burbujas de mensaje: Derecha para mí(user logueado), izq para ellos */
        .msg-bubble { max-width: 75%; padding: 10px 15px; border-radius: 15px; margin-bottom: 10px; font-size: 0.9rem; }
        .msg-me { background: #3bb3bd; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
        .msg-them { background: #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; }
        
        /*Indicador de contacto activo*/
        .contact-item.active { background-color: #f0f7f8 !important; border-left: 4px solid #3bb3bd; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top mb-3">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary-custom" href="dashboard-alumno.php">ISIMatch</a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <div class="dropdown">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:35px; height:35px; cursor:pointer;" data-bs-toggle="dropdown">
                <?php echo $inicial_mia; ?>
            </div>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3">
                <li><a class="dropdown-item py-2" href="ficha-alumno.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                <li><a class="dropdown-item py-2" href="pagos-alumno.php"><i class="bi bi-credit-card me-2"></i> Pagos y Facturas</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger py-2" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
  </div>
</nav>

<div class="container flex-grow-1">
    <div class="row chat-container shadow-sm g-0">
        
        <div class="col-md-4 contacts-column bg-white">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Mensajes</h5>
                <a href="catalogo.php" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-search"></i></a>
            </div>
            <div class="list-group list-group-flush">
                <?php if($res_contactos && $res_contactos->num_rows > 0): ?>
                    <?php while($c = $res_contactos->fetch_assoc()): ?>
                        <a href="mensajes.php?con=<?php echo $c['contacto_id']; ?>" 
                           class="list-group-item list-group-item-action contact-item border-0 p-3 <?php echo ($chat_con == $c['contacto_id']) ? 'active' : ''; ?>">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:45px; height:45px;">
                                    <?php echo strtoupper(substr($c['nombre'], 0, 1)); ?>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 small fw-bold"><?php echo htmlspecialchars($c['nombre']); ?></h6>
                                    <small class="text-muted text-truncate d-block small"><?php echo htmlspecialchars($c['ultimo_msj'] ?? 'Enviado recientemente'); ?></small>
                                </div>
                                <?php if($c['no_leidos'] > 0): ?>
                                    <span class="badge rounded-pill bg-danger"><?php echo $c['no_leidos']; ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted p-4 small">No tienes conversaciones activas.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column bg-white">
            <?php if($chat_con): ?>
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($info_contacto['nombre'] . " " . $info_contacto['apellidos']); ?></h6>
                        <span class="badge bg-light text-dark border small fw-normal"><?php echo ucfirst($info_contacto['rol']); ?></span>
                    </div>
                </div>

                <div class="messages-area d-flex flex-column" id="chatBox">
                    <?php foreach($mensajes_chat as $m): ?>
                        <div class="msg-bubble shadow-sm <?php echo ($m['remitente_id'] == $mi_id) ? 'msg-me' : 'msg-them'; ?>">
                            <?php echo htmlspecialchars($m['contenido']); ?>
                            <div class="small opacity-50 mt-1" style="font-size: 0.6rem; text-align: right;">
                                <?php echo date('H:i', strtotime($m['fecha_envio'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form action="../PHP/mensaje_privado.php" method="POST" class="p-3 border-top">
                    <input type="hidden" name="destinatario_id" value="<?php echo $chat_con; ?>">
                    <div class="input-group">
                        <input type="text" name="contenido" class="form-control border-0 bg-light rounded-pill px-4" placeholder="Escribe un mensaje..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary-custom rounded-circle ms-2" style="width:40px; height:40px;"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>
            <?php else: ?>
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                    <i class="bi bi-chat-dots fs-1 mb-2"></i>
                    <p>Selecciona un contacto para chatear</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /**
     *Esta pequeña lógica hace que, al abrir el chat,
     la pantalla baje automáticamente al último mensaje enviado
     */
    const chatBox = document.getElementById('chatBox');
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>