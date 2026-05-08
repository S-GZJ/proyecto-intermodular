<?php
// --- LÓGICA DE SERVIDOR (PHP) ---
session_start();

/*-- ESCUDO DE SEGURIDAD --*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: login.php");
    exit();
}

include '../PHP/conexion.php';
$alumno_id = $_SESSION['usuario_id'];

// 1. OBTENER ESTADÍSTICAS REALES
$sql_stats = "SELECT 
                COUNT(id) as total_clases, 
                SUM(duracion_minutos)/60 as total_horas,
                COUNT(DISTINCT profesor_id) as total_tutores
              FROM clases 
              WHERE alumno_id = '$alumno_id' AND estado = 'completada'";
$res_stats = $conn->query($sql_stats);
$stats = $res_stats->fetch_assoc();

// 2. OBTENER LA CLASE "EN UNAS HORAS"
$sql_proxima = "SELECT c.*, u.nombre as profe_nombre, u.apellidos as profe_apellidos 
                FROM clases c 
                JOIN usuarios u ON c.profesor_id = u.id 
                WHERE c.alumno_id = '$alumno_id' AND c.estado = 'aceptada' AND c.fecha_hora > NOW() 
                ORDER BY c.fecha_hora ASC LIMIT 1";
$res_proxima = $conn->query($sql_proxima);
$proxima = $res_proxima->fetch_assoc();

// 3. OBTENER MIS RESERVAS PENDIENTES
$sql_reservas = "SELECT c.*, u.nombre as profe_nombre 
                 FROM clases c 
                 JOIN usuarios u ON c.profesor_id = u.id 
                 WHERE c.alumno_id = '$alumno_id' AND c.estado = 'pendiente' 
                 ORDER BY c.fecha_hora ASC";
$res_reservas = $conn->query($sql_reservas);

// 4. OBTENER CLASES RECIENTES
$sql_recientes = "SELECT c.*, u.nombre as profe_nombre 
                  FROM clases c 
                  JOIN usuarios u ON c.profesor_id = u.id 
                  WHERE c.alumno_id = '$alumno_id' AND c.estado = 'completada' 
                  ORDER BY c.fecha_hora DESC LIMIT 3";
$res_recientes = $conn->query($sql_recientes);

// 5. NUEVO: CONTADOR DE MENSAJES NO LEÍDOS
$sql_msj = "SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = '$alumno_id' AND leido = 0";
$res_msj = $conn->query($sql_msj);
$msj_data = $res_msj->fetch_assoc();
$mensajes_pendientes = $msj_data['total'];

$nombre_alumno = htmlspecialchars($_SESSION['nombre']);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Alumno - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold text-primary-custom" href="../index.php">ISIMatch</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="mensajes.php" class="text-dark position-relative me-2">
                <i class="bi bi-chat-dots fs-5"></i>
                <?php if($mensajes_pendientes > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        <?php echo $mensajes_pendientes; ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="dropdown">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px; cursor:pointer;" data-bs-toggle="dropdown">
                    <?php echo strtoupper(substr($nombre_alumno, 0, 1)); ?>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3">
                    <li><a class="dropdown-item py-2" href="ficha-alumno.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger py-2" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
      </div>
    </nav>

    <main class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">¡Hola, <?php echo $nombre_alumno; ?>! 👋</h2>
                <p class="text-muted">Es un buen día para aprender algo nuevo.</p>
            </div>
        </div>

        <div class="row g-3 mb-5 text-center">
            <div class="col-4">
                <div class="card border-0 shadow-sm p-3 rounded-4">
                    <h3 class="fw-bold mb-0"><?php echo (int)$stats['total_clases']; ?></h3>
                    <small class="text-muted fw-bold">CLASES</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm p-3 rounded-4">
                    <h3 class="fw-bold mb-0"><?php echo round($stats['total_horas'], 1); ?>h</h3>
                    <small class="text-muted fw-bold">HORAS</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm p-3 rounded-4">
                    <h3 class="fw-bold mb-0"><?php echo (int)$stats['total_tutores']; ?></h3>
                    <small class="text-muted fw-bold">TUTORES</small>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                
                <h5 class="fw-bold mb-3">Tu próxima sesión</h5>
                <?php if($proxima): ?>
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white border-start border-primary border-5 mb-4 position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 rounded-pill fw-bold">CONFIRMADA</span>
                                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($proxima['materia_nombre_manual']); ?></h4>
                                <p class="text-muted mb-3">Con el Prof. <strong><?php echo htmlspecialchars($proxima['profe_nombre']); ?></strong></p>
                                <div class="d-flex gap-3 text-dark small">
                                    <span><i class="bi bi-calendar3 me-1"></i> <?php echo date('d M', strtotime($proxima['fecha_hora'])); ?></span>
                                    <span><i class="bi bi-clock me-1"></i> <?php echo date('H:i', strtotime($proxima['fecha_hora'])); ?></span>
                                </div>
                            </div>
                            <a href="videollamada.php?id=<?php echo $proxima['id']; ?>" class="btn btn-primary-custom px-4 py-2 rounded-pill fw-bold shadow-sm">ENTRAR AL AULA</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm p-5 text-center rounded-4 mb-4 bg-white border-dashed">
                        <i class="bi bi-calendar-x text-muted fs-1 mb-3"></i>
                        <p class="text-muted">No tienes clases confirmadas para hoy.</p>
                        <a href="catalogo.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-bold">Explorar profesores</a>
                    </div>
                <?php endif; ?>

                <h5 class="fw-bold mb-3">Mis Reservas enviadas</h5>
                <div class="row g-3">
                    <?php if($res_reservas->num_rows > 0): ?>
                        <?php while($res = $res_reservas->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm p-3 rounded-4 bg-white h-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($res['materia_nombre_manual']); ?></h6>
                                            <small class="text-muted d-block">Prof. <?php echo htmlspecialchars($res['profe_nombre']); ?></small>
                                            <small class="fw-bold text-primary mt-2 d-block"><i class="bi bi-clock"></i> <?php echo date('d M, H:i', strtotime($res['fecha_hora'])); ?></small>
                                        </div>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle small px-2">Pendiente</span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12"><p class="small text-muted fst-italic">No tienes solicitudes pendientes en este momento.</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">Recientes</h5>
                <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                    <?php if($res_recientes->num_rows > 0): ?>
                        <?php while($rec = $res_recientes->fetch_assoc()): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom last-child-border-0">
                                <div class="overflow-hidden">
                                    <h6 class="mb-0 fw-bold small text-truncate"><?php echo htmlspecialchars($rec['materia_nombre_manual']); ?></h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Prof. <?php echo htmlspecialchars($rec['profe_nombre']); ?></small>
                                </div>
                                <a href="valorar.php?id=<?php echo $rec['id']; ?>" class="btn btn-light btn-sm rounded-circle" title="Valorar clase"><i class="bi bi-star"></i></a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="small text-muted text-center py-4">Aún no has completado ninguna clase.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>