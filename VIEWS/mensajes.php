<?php
session_start();
include '../PHP/conexion.php';

// 1. ESCUDO DE SEGURIDAD
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mi_id = $_SESSION['usuario_id'];

// 2. OBTENER LISTA DE CONVERSACIONES CON CONTEO DE NO LEÍDOS
// Esta consulta es avanzada: une usuarios con el último mensaje y cuenta cuántos tienes sin leer de cada uno
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

// 3. OBTENER CHAT SELECCIONADO
$chat_con = isset($_GET['con']) ? $conn->real_escape_string($_GET['con']) : null;
$mensajes_chat = [];

if ($chat_con) {
    // Marcamos como leídos los mensajes que me enviaron ellos al abrir el chat
    $conn->query("UPDATE mensajes SET leido = 1 WHERE remitente_id = '$chat_con' AND destinatario_id = '$mi_id'");

    $sql_chat = "SELECT * FROM mensajes 
                 WHERE (remitente_id = '$mi_id' AND destinatario_id = '$chat_con')
                    OR (remitente_id = '$chat_con' AND destinatario_id = '$mi_id')
                 ORDER BY fecha_envio ASC";
    $res_chat = $conn->query($sql_chat);
    while($row = $res_chat->fetch_assoc()){
        $mensajes_chat[] = $row;
    }
    
    $info_contacto = $conn->query("SELECT nombre, apellidos, rol FROM usuarios WHERE id = '$chat_con'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes Privados - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        body { background-color: #f0f2f5; }
        .chat-container { height: 85vh; background: white; border-radius: 20px; overflow: hidden; }
        .contacts-column { border-right: 1px solid #f0f2f5; overflow-y: auto; }
        .chat-column { display: flex; flex-direction: column; background: #fff; }
        .messages-area { flex-grow: 1; overflow-y: auto; padding: 25px; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); background-color: #f9f9f9; }
        
        /* Burbujas de Chat Estilo Moderno */
        .msg-bubble { max-width: 70%; padding: 12px 16px; border-radius: 18px; margin-bottom: 8px; font-size: 0.95rem; position: relative; shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .msg-me { background: var(--primary-color); color: white; align-self: flex-end; border-bottom-right-radius: 4px; }
        .msg-them { background: white; color: #333; align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid #e9ecef; }
        
        .contact-item { border: none; margin: 5px 10px; border-radius: 12px; transition: 0.2s; }
        .contact-item.active { background-color: #f0f7f8 !important; }
        .unread-dot { width: 10px; height: 10px; background-color: var(--primary-color); border-radius: 50%; }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="row chat-container shadow-lg g-0">
        
        <div class="col-md-4 contacts-column bg-white">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Mensajes</h4>
                <a href="catalogo.php" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-plus-lg"></i></a>
            </div>
            
            <div class="list-group list-group-flush mt-2">
                <?php if($res_contactos->num_rows > 0): ?>
                    <?php while($c = $res_contactos->fetch_assoc()): ?>
                        <a href="mensajes.php?con=<?php echo $c['contacto_id']; ?>" 
                           class="list-group-item list-group-item-action contact-item p-3 <?php echo ($chat_con == $c['contacto_id']) ? 'active' : ''; ?>">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:50px; height:50px; font-size: 1.2rem;">
                                        <?php echo strtoupper(substr($c['nombre'], 0, 1)); ?>
                                    </div>
                                    <?php if($c['no_leidos'] > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.7rem;">
                                            <?php echo $c['no_leidos']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-truncate"><?php echo htmlspecialchars($c['nombre'] . " " . $c['apellidos']); ?></h6>
                                    </div>
                                    <small class="text-muted text-truncate d-block">
                                        <?php echo $c['ultimo_msj'] ? htmlspecialchars(substr($c['ultimo_msj'], 0, 30)).'...' : 'Empezar conversación'; ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center p-5">
                        <i class="bi bi-chat-heart text-muted fs-1"></i>
                        <p class="text-muted mt-2">Busca un profesor para empezar a chatear.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8 chat-column">
            <?php if($chat_con): ?>
                <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:45px; height:45px;">
                            <?php echo strtoupper(substr($info_contacto['nombre'], 0, 1)); ?>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($info_contacto['nombre'] . " " . $info_contacto['apellidos']); ?></h6>
                            <small class="text-success small"><i class="bi bi-dot"></i> En línea</small>
                        </div>
                    </div>
                    <a href="ficha-<?php echo ($info_contacto['rol'] == 'profesor') ? 'profesor' : 'alumno'; ?>.php?id=<?php echo $chat_con; ?>" class="btn btn-outline-primary btn-sm rounded-pill">Ver perfil</a>
                </div>

                <div class="messages-area d-flex flex-column" id="chatBox">
                    <?php foreach($mensajes_chat as $m): ?>
                        <div class="msg-bubble shadow-sm <?php echo ($m['remitente_id'] == $mi_id) ? 'msg-me' : 'msg-them'; ?>">
                            <?php echo htmlspecialchars($m['contenido']); ?>
                            <div class="small opacity-75 mt-1" style="font-size: 0.65rem; text-align: right;">
                                <?php echo date('H:i', strtotime($m['fecha_envio'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form action="../PHP/enviar_mensaje_privado.php" method="POST" class="p-3 bg-white border-top shadow-sm">
                    <input type="hidden" name="destinatario_id" value="<?php echo $chat_con; ?>">
                    <div class="input-group">
                        <input type="text" name="contenido" class="form-control border-0 bg-light rounded-pill px-4 me-2" placeholder="Escribe un mensaje..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary-custom rounded-circle" style="width:45px; height:45px;"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>
            <?php else: ?>
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="bi bi-chat-dots fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Tus conversaciones</h5>
                    <p>Selecciona a alguien de la lista para empezar a hablar.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    const chatBox = document.getElementById('chatBox');
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>