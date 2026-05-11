<?php
/**
 * ISIMatch - Perfil Público del Profesor
 * Este archivo muestra la biografía, valoraciones y el formulario de reserva de un tutor
 */

//--LÓGICA DE SERVIDOR (PHP)--
session_start();

/*--ESCUDO DE SEGURIDAD--*/
// Solo usuarios registrados (alumnos o profesores) pueden ver perfiles detallados
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

/*Conectar con la base de datos*/
include '../PHP/conexion.php';

/*--IDENTIFICACIÓN DEL PERFIL--*/
//Si recibimos un ID por la URL (?id=5), mostramos ese perfil
//Si no, mostramos el perfil del propio usuario logueado (vista previa)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $perfil_id = $conn->real_escape_string($_GET['id']);
} else {
    $perfil_id = $_SESSION['usuario_id'];
}

/*--CONSULTA SQL DINÁMICA--*/
//Obtenemos los datos personales y profesionales del profesor solicitado
$sql = "SELECT u.id, u.nombre, u.apellidos, pd.titulo_profesional, pd.bio, pd.tarifa_hora, pd.valoracion_media, pd.total_resenas 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$perfil_id' AND u.rol = 'profesor'";

$resultado = $conn->query($sql);
$profe = $resultado->fetch_assoc();

//Si el ID no pertenece a un profesor o no existe, detenemos la ejecución.
if (!$profe) {
    die("El perfil solicitado no existe o no está disponible. <a href='catalogo.php'>Volver al catálogo</a>");
}

//Variables auxiliares para el diseño
$nombre_completo = htmlspecialchars($profe['nombre'] . " " . $profe['apellidos']);
$letra_avatar = strtoupper(substr($profe['nombre'], 0, 1));
$es_mi_propio_perfil = ($_SESSION['usuario_id'] == $perfil_id);

/*--CONSULTA DE RESEÑAS REALES--*/
//Traemos las últimas 3 opiniones de alumnos 
$sql_resenas = "SELECT r.*, u.nombre FROM resenas r JOIN usuarios u ON r.alumno_id = u.id WHERE r.profesor_id = '$perfil_id' ORDER BY r.fecha_resena DESC LIMIT 3";
$res_resenas = $conn->query($sql_resenas);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $nombre_completo; ?> - Tutor en ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">

    <nav class="navbar navbar-light bg-white border-bottom sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="catalogo.php">
          <i class="bi bi-arrow-left"></i> <span class="small">Volver a la búsqueda</span>
        </a>
      </div>
    </nav>

    <div class="container my-5">
        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:110px; height:110px; font-size: 2.8rem;">
                            <?php echo $letra_avatar; ?>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h1 class="fw-bold h2 mb-0"><?php echo $nombre_completo; ?></h1>
                                <i class="bi bi-patch-check-fill text-primary" title="Perfil Verificado"></i>
                            </div>
                            <p class="text-primary-custom fw-bold fs-5 mb-1">
                                <?php echo htmlspecialchars($profe['titulo_profesional'] ?? 'Tutor Especialista'); ?>
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-warning">
                                    <i class="bi bi-star-fill"></i> <?php echo $profe['valoracion_media'] ?? '5.0'; ?>
                                </span>
                                <span class="text-muted small">(<?php echo $profe['total_resenas'] ?? '0'; ?> reseñas)</span>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-2">Disponible hoy</span>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold border-bottom pb-2 mb-3">Presentación</h5>
                    <p class="text-muted lh-lg">
                        <?php echo nl2br(htmlspecialchars($profe['bio'] ?? 'Este profesor está preparando su biografía detallada para ti.')); ?>
                    </p>
                </div>

                <h5 class="fw-bold mb-3 mt-5">Lo que dicen sus alumnos</h5>
                <div class="row g-3">
                    <?php if($res_resenas->num_rows > 0): ?>
                        <?php while($r = $res_resenas->fetch_assoc()): ?>
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold small"><?php echo htmlspecialchars($r['nombre']); ?></span>
                                    <div class="text-warning small">
                                        <?php for($i=0; $i<$r['puntuacion']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0 fst-italic">"<?php echo htmlspecialchars($r['comentario']); ?>"</p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12"><p class="text-muted small">Este profesor aún no tiene reseñas. ¡Sé el primero en aprender con él!</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top bg-white" style="top: 100px;">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <small class="text-muted fw-bold text-uppercase">Tarifa por hora</small>
                        <h2 class="fw-bold text-dark mb-0"><?php echo number_format($profe['tarifa_hora'] ?? 15, 2); ?>€ <span class="fs-6 text-muted fw-normal">/h</span></h2>
                    </div>

                    <?php if(!$es_mi_propio_perfil): ?>
                        <form action="../PHP/formulario_reserva_profe.php" method="POST">
                            <input type="hidden" name="profesor_id" value="<?php echo $perfil_id; ?>">
                            <input type="hidden" name="precio_hora" value="<?php echo $profe['tarifa_hora']; ?>">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">MATERIA</label>
                                <input type="text" name="materia" class="form-control bg-light border-0 py-2" placeholder="¿Qué quieres estudiar?" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">FECHA Y HORA</label>
                                <input type="datetime-local" name="fecha_hora" class="form-control bg-light border-0 py-2" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">DURACIÓN</label>
                                <select name="duracion" class="form-select bg-light border-0 py-2">
                                    <option value="60">1 Hora</option>
                                    <option value="90">1.5 Horas</option>
                                    <option value="120">2 Horas</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold rounded-pill shadow-sm mb-3">
                                <i class="bi bi-calendar-check me-2"></i> SOLICITAR RESERVA
                            </button>
                        </form>
                        
                        <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                            <i class="bi bi-shield-lock-fill text-success"></i>
                            <span>Reserva protegida por ISIMatch</span>
                        </div>
                    
                    <?php else: ?>
                        <div class="text-center">
                            <div class="alert alert-secondary border-0 small mb-4 py-3">
                                <i class="bi bi-eye-fill me-2"></i> Vista previa de tu perfil público.
                            </div>
                            <a href="configuracion-profesor.php" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> EDITAR MI PERFIL
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card border-0 shadow-sm p-3 mt-4 rounded-4 bg-white">
                    <h6 class="fw-bold small mb-3">Detalles del Tutor</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-translate text-primary"></i> <span>Idiomas: Español, Inglés (C1)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-clock-history text-primary"></i> <span>Responde en menos de 1h</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>