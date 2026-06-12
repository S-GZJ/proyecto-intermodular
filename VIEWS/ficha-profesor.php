<?php
/**
 * ISIMatch - Perfil Público del Profesor
 * Este archivo muestra la biografía, valoraciones y permite la edición rápida si es el dueño.
 */

//--LÓGICA DE SERVIDOR (PHP)--
session_start();

/*--ESCUDO DE SEGURIDAD--*/
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

/*Conectar con la base de datos*/
include '../PHP/conexion.php';

$mi_id = $_SESSION['usuario_id'];

/*--IDENTIFICACIÓN DEL PERFIL--*/
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $perfil_id = $conn->real_escape_string($_GET['id']);
} else {
    $perfil_id = $mi_id;
}

$es_mi_propio_perfil = ($mi_id == $perfil_id);

/*-- LÓGICA DE ACTUALIZACIÓN RÁPIDA --*/
// Si el profesor envía el formulario de edición rápida desde esta misma página
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_detalles_rapido'])) {

    $nuevos_idiomas = $conn->real_escape_string($_POST['idiomas']);
    $nuevo_tiempo = $conn->real_escape_string($_POST['tiempo_respuesta']);

    $sql_update = "UPDATE profesores_detalles
                   SET idiomas = '$nuevos_idiomas',
                       tiempo_respuesta = '$nuevo_tiempo'
                   WHERE usuario_id = '$mi_id'";

    if ($conn->query($sql_update)) {
        header("Location: ficha-profesor.php?id=" . $perfil_id);
        exit();
    }
}
/*--CONSULTA SQL DINÁMICA ACTUALIZADA--*/
$sql = "SELECT u.id, u.nombre, u.apellidos,
               pd.titulo_profesional,
               pd.bio,
               pd.tarifa_hora,
               pd.valoracion_media,
               pd.total_resenas,
               pd.idiomas,
               pd.tiempo_respuesta
        FROM usuarios u
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id
        WHERE u.id = '$perfil_id' AND u.rol = 'profesor'";

$resultado = $conn->query($sql);
$profe = $resultado->fetch_assoc();

if (!$profe) {
    die("El perfil solicitado no existe o no está disponible. <a href='catalogo.php'>Volver al catálogo</a>");
}

//Variables auxiliares
$nombre_completo = htmlspecialchars($profe['nombre'] . " " . $profe['apellidos']);
$letra_avatar = strtoupper(substr($profe['nombre'], 0, 1));

/*--CONSULTA DE RESEÑAS REALES (hasta 5 recientes) --*/
$sql_resenas = "SELECT r.puntuacion, r.comentario, r.fecha_resena, u.nombre
                FROM resenas r
                JOIN usuarios u ON r.alumno_id = u.id
                WHERE r.profesor_id = '$perfil_id'
                ORDER BY r.fecha_resena DESC
                LIMIT 5";
$res_resenas = $conn->query($sql_resenas);

/*--DISTRIBUCIÓN DE PUNTUACIONES (para las barras de progreso) --*/
$sql_dist = "SELECT puntuacion, COUNT(*) as cantidad
             FROM resenas
             WHERE profesor_id = '$perfil_id'
             GROUP BY puntuacion
             ORDER BY puntuacion DESC";
$res_dist = $conn->query($sql_dist);
$distribucion = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
while ($d = $res_dist->fetch_assoc()) {
    $distribucion[(int)$d['puntuacion']] = (int)$d['cantidad'];
}
// Número total de valoraciones almacenado en profesores_detalles.
// Se utiliza para calcular el porcentaje de cada barra.
$total_val = $profe['total_resenas'] ?? 0;

// Mostrar banner de éxito si viene recién de valorar
$valoracion_ok = (isset($_GET['status']) && $_GET['status'] === 'valoracion_ok');
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

                <!-- BANNER DE ÉXITO POST-VALORACIÓN -->
                <?php if ($valoracion_ok): ?>
                <div class="alert border-0 rounded-4 d-flex align-items-center gap-3 mb-4"
                     style="background:linear-gradient(135deg,rgba(59,179,189,0.12),rgba(59,179,189,0.04)); border-left:4px solid var(--primary-color) !important;">
                    <i class="bi bi-star-fill fs-4" style="color:var(--primary-color);"></i>
                    <div>
                        <strong class="d-block">¡Gracias por tu valoración!</strong>
                        <small class="text-muted">Tu opinión ayuda a otros alumnos a encontrar el mejor tutor.</small>
                    </div>
                </div>
                <?php endif; ?>

                <h5 class="fw-bold mb-3 mt-2">Lo que dicen sus alumnos</h5>

                <!-- RESUMEN ESTADÍSTICO DE VALORACIONES -->
                <?php if ($total_val > 0): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="row align-items-center g-3">
                        <!-- Puntuación global -->
                        <div class="col-auto text-center" style="min-width:120px;">
                            <div class="fw-bold" style="font-size:3rem; line-height:1; color:var(--text-dark);">
                                <?php echo number_format($profe['valoracion_media'], 1); ?>
                            </div>
                            <div class="text-warning my-1" style="font-size:1.1rem;">
                                <?php
                                $media_r = round($profe['valoracion_media']);
                                for($i=1;$i<=5;$i++) {
                                    echo ($i <= $media_r)
                                        ? '<i class="bi bi-star-fill"></i>'
                                        : '<i class="bi bi-star text-muted"></i>';
                                }
                                ?>
                            </div>
                            <small class="text-muted"><?php echo $total_val; ?> valoraci<?php echo $total_val == 1 ? 'ón' : 'ones'; ?></small>
                        </div>
                        <!-- Barras de distribución -->
                        <div class="col">
                            <?php for($s=5; $s>=1; $s--): ?>
                            <?php $pct = $total_val > 0 ? round(($distribucion[$s] / $total_val) * 100) : 0; ?>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-muted" style="font-size:0.75rem; width:14px; text-align:right;"><?php echo $s; ?></span>
                                <i class="bi bi-star-fill text-warning" style="font-size:0.65rem;"></i>
                                <div class="flex-grow-1 rounded-pill" style="height:8px; background:#f0f0f0; overflow:hidden;">
                                    <div class="rounded-pill h-100" style="width:<?php echo $pct; ?>%; background:var(--primary-color); transition:width 0.6s ease;"></div>
                                </div>
                                <span class="text-muted" style="font-size:0.72rem; width:20px;"><?php echo $distribucion[$s]; ?></span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- LISTADO DE RESEÑAS -->
                <div class="row g-3">
                    <?php if($res_resenas->num_rows > 0): ?>
                        <?php while($r = $res_resenas->fetch_assoc()): ?>
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                             style="width:36px; height:36px; font-size:0.9rem; flex-shrink:0;">
                                            <?php echo strtoupper(substr($r['nombre'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold small d-block"><?php echo htmlspecialchars($r['nombre']); ?></span>
                                            <small class="text-muted" style="font-size:0.7rem;">
                                                <?php echo date('d M Y', strtotime($r['fecha_resena'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-warning small">
                                        <?php for($i=1;$i<=5;$i++) {
                                            echo ($i <= $r['puntuacion'])
                                                ? '<i class="bi bi-star-fill"></i>'
                                                : '<i class="bi bi-star text-muted"></i>';
                                        } ?>
                                    </div>
                                </div>
                                <?php if (!empty($r['comentario'])): ?>
                                <p class="text-muted small mb-0 fst-italic ps-1">&ldquo;<?php echo nl2br(htmlspecialchars($r['comentario'])); ?>&rdquo;</p>
                                <?php else: ?>
                                <p class="text-muted small mb-0 fst-italic">Sin comentario adicional.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white text-center">
                                <i class="bi bi-chat-square-text text-muted fs-3 mb-2"></i>
                                <p class="text-muted small mb-0">Este profesor aún no tiene reseñas. ¡Sé el primero en aprender con él!</p>
                            </div>
                        </div>
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
                    <?php else: ?>
                        <div class="text-center">
                            <div class="alert alert-secondary border-0 small mb-4 py-3">
                                <i class="bi bi-eye-fill me-2"></i> Vista previa de tu perfil público.
                            </div>
                            <a href="configuracion-profesor.php" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> IR A CONFIGURACIÓN
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card border-0 shadow-sm p-4 mt-4 rounded-4 bg-white">
                    <h6 class="fw-bold small mb-3 text-uppercase text-muted">Detalles del Tutor</h6>
                    
                    <?php if ($es_mi_propio_perfil): ?>
                        <form action="ficha-profesor.php?id=<?php echo $perfil_id; ?>" method="POST">
                            <input type="hidden" name="actualizar_detalles_rapido" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold"><i class="bi bi-translate text-primary me-1"></i> IDIOMAS</label>
                                <input type="text" name="idiomas" class="form-control form-control-sm bg-light border-0" 
                                       value="<?php echo htmlspecialchars($profe['idiomas'] ?? 'Español'); ?>" placeholder="Ej: Español, Inglés">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"><i class="bi bi-clock-history text-primary me-1"></i> RESPUESTA</label>
                                <select name="tiempo_respuesta" class="form-select form-select-sm bg-light border-0">
                                    <option value="menos de 1h" <?php echo ($profe['tiempo_respuesta'] == 'menos de 1h') ? 'selected' : ''; ?>>menos de 1h</option>
                                    <option value="unas pocas horas" <?php echo ($profe['tiempo_respuesta'] == 'unas pocas horas') ? 'selected' : ''; ?>>unas pocas horas</option>
                                    <option value="menos de 24h" <?php echo ($profe['tiempo_respuesta'] == 'menos de 24h') ? 'selected' : ''; ?>>menos de 24h</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary-custom btn-sm w-100 rounded-pill shadow-sm">
                                <i class="bi bi-save me-1"></i> Guardar cambios
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-translate text-primary fs-5"></i> 
                                <span>Idiomas: <strong><?php echo htmlspecialchars($profe['idiomas'] ?? 'Español'); ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-clock-history text-primary fs-5"></i> 
                                <span>Responde en: <strong><?php echo htmlspecialchars($profe['tiempo_respuesta'] ?? 'menos de 24h'); ?></strong></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>