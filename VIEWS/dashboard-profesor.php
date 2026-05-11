<?php
/**
 * ISIMatch - Dashboard del Profesor
 * Este archivo gestiona la vista principal para los profesores, mostrando
 * estadísticas de ingresos, gestión de solicitudes y próximas sesiones.
 */

//--LÓGICA DE SERVIDOR (PHP)---

//Iniciamos la sesión para identificar al usuario
session_start(); 

/*--ESCUDO DE SEGURIDAD--*/
//Verificamos que el usuario esté logueado y que sea específicamente un profesor
//Si no lo es, lo redirigimos al login para proteger la información
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

//Conexión a la base de datos
include '../PHP/conexion.php';
$profesor_id = $_SESSION['usuario_id'];

//--CONSULTAS DE DATOS (Dinamismo del Panel)--

//--OBTENER PRÓXIMA CLASE ACEPTADA--
//Buscamos la clase confirmada más cercana en el tiempo (fecha > NOW) que aún no ha ocurrido
$sql_proxima = "SELECT c.*, u.nombre, u.apellidos 
                FROM clases c 
                JOIN usuarios u ON c.alumno_id = u.id 
                WHERE c.profesor_id = '$profesor_id' AND c.estado = 'aceptada' AND c.fecha_hora > NOW() 
                ORDER BY c.fecha_hora ASC LIMIT 1";
$res_proxima = $conn->query($sql_proxima);
$proxima = $res_proxima->fetch_assoc();

//--SOLICITUDES PENDIENTES--
//Obtenemos las clases que los alumnos han reservado pero que el profesor aún no ha aceptado
$sql_pendientes = "SELECT c.*, u.nombre, u.apellidos 
                   FROM clases c 
                   JOIN usuarios u ON c.alumno_id = u.id 
                   WHERE c.profesor_id = '$profesor_id' AND c.estado = 'pendiente' 
                   ORDER BY c.fecha_hora ASC";
$res_pendientes = $conn->query($sql_pendientes);

//--ESTADÍSTICAS GLOBALES--
//Usamos subconsultas para obtener en una sola fila: Ingresos totales,
//número de alumnos distintos y mensajes nuevos.
$sql_stats = "SELECT 
                (SELECT SUM(precio_total) FROM clases WHERE profesor_id = '$profesor_id' AND estado = 'completada') as ingresos, 
                (SELECT COUNT(DISTINCT alumno_id) FROM clases WHERE profesor_id = '$profesor_id') as alumnos,
                (SELECT COUNT(*) FROM mensajes WHERE destinatario_id = '$profesor_id' AND leido = 0) as msjs_nuevos";
$res_stats = $conn->query($sql_stats);
$stats = $res_stats->fetch_assoc();

//--ALUMNOS RECIENTES--
//Lista de los últimos 4 alumnos con los que el profesor ha tenido contacto para facilitar el seguimiento.
$sql_alumnos = "SELECT DISTINCT u.id, u.nombre, u.apellidos 
                FROM usuarios u 
                JOIN clases c ON u.id = c.alumno_id 
                WHERE c.profesor_id = '$profesor_id' 
                ORDER BY c.fecha_hora DESC LIMIT 4";
$res_alumnos = $conn->query($sql_alumnos);

//Preparamos el nombre y la inicial para la interfaz
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
$inicial = strtoupper(substr($nombre_usuario, 0, 1));
?>


<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Profesor - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="../index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i> ISIMatch
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="mensajes.php" class="text-dark position-relative me-2">
                <i class="bi bi-chat-dots fs-5"></i>
                <?php if($stats['msjs_nuevos'] > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        <?php echo $stats['msjs_nuevos']; ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;"><?php echo $inicial; ?></div>
                <span class="d-none d-md-inline"><?php echo $nombre_usuario; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item py-2" href="ficha-profesor.php"><i class="bi bi-person me-2"></i> Mi perfil</a></li>
                <li><a class="dropdown-item py-2" href="configuracion-profesor.php"><i class="bi bi-gear me-2"></i> Configuración</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger py-2" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
              </ul>
            </div>
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <header class="row g-4 align-items-center mb-5">
        <div class="col-md-6">
          <h2 class="fw-bold text-dark mb-1">¡Hola de nuevo, <?php echo $nombre_usuario; ?>!</h2>
          <p class="text-muted mb-0">Tienes <?php echo $res_pendientes->num_rows; ?> solicitudes nuevas esperando respuesta.</p>
        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card p-3 border-0 shadow-sm text-center rounded-4">
                        <small class="text-muted fw-bold d-block mb-1">INGRESOS</small>
                        <span class="fw-bold text-success fs-4"><?php echo number_format($stats['ingresos'] ?? 0, 2); ?>€</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card p-3 border-0 shadow-sm text-center rounded-4">
                        <small class="text-muted fw-bold d-block mb-1">ALUMNOS</small>
                        <span class="fw-bold text-dark fs-4"><?php echo $stats['alumnos'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </div>
      </header>

      <div class="row g-4">
        <section class="col-lg-8">
          
          <h5 class="fw-bold mb-3">Próxima sesión confirmada</h5>
          <?php if($proxima): ?>
            <article class="card card-custom p-4 mb-4 border-start border-5 border-primary shadow-sm border-0 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                  <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2 fw-bold">FECHA CONFIRMADA</span>
                    <h4 class="mb-1 fw-bold"><?php echo htmlspecialchars($proxima['materia_nombre_manual']); ?></h4>
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-person-circle me-1"></i> Alumno: <?php echo htmlspecialchars($proxima['nombre'] . " " . $proxima['apellidos']); ?>
                    </p>
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-clock me-1"></i> <?php echo date('d M, H:i', strtotime($proxima['fecha_hora'])); ?> (<?php echo $proxima['duracion_minutos']; ?> min)
                    </p>
                  </div>
                  <a href="videollamada.php?id=<?php echo $proxima['id']; ?>" class="btn btn-primary-custom px-4 py-2 rounded-pill fw-bold">ENTRAR AL AULA</a>
                </div>
            </article>
          <?php else: ?>
            <div class="card border-dashed p-5 text-center rounded-4 mb-4 bg-white">
                <p class="text-muted mb-0">No tienes clases para hoy. ¡Aprovecha para revisar tus mensajes!</p>
            </div>
          <?php endif; ?>

          <h5 class="fw-bold mb-3 mt-4">Mis Alumnos Recientes</h5>
          <div class="row g-3 mb-4">
              <?php while($alum = $res_alumnos->fetch_assoc()): ?>
              <div class="col-sm-6">
                  <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
                      <div class="d-flex align-items-center gap-3">
                          <div class="avatar-inicial small" style="background:#f0f2f5; color:#333; font-weight:bold; width:35px; height:35px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                              <?php echo strtoupper(substr($alum['nombre'], 0, 1)); ?>
                          </div>
                          <div class="flex-grow-1">
                              <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($alum['nombre'] . " " . $alum['apellidos']); ?></h6>
                          </div>
                          <a href="mensajes.php?con=<?php echo $alum['id']; ?>" class="btn btn-light btn-sm rounded-circle"><i class="bi bi-chat-text"></i></a>
                      </div>
                  </div>
              </div>
              <?php endwhile; ?>
          </div>
        </section>

        <aside class="col-lg-4">
          <div class="card card-custom p-4 shadow-sm border-0 rounded-4 bg-white">
            <h5 class="fw-bold mb-4">Nuevas Solicitudes</h5>
            
            <?php if($res_pendientes->num_rows > 0): ?>
                <?php while($sol = $res_pendientes->fetch_assoc()): ?>
                    <div class="solicitud p-3 bg-light border-0 rounded-4 mb-3">
                      <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold border" style="width:40px; height:40px;">
                            <?php echo strtoupper(substr($sol['nombre'], 0, 1)); ?>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($sol['nombre']); ?></h6>
                            <small class="text-primary-custom fw-bold"><?php echo htmlspecialchars($sol['materia_nombre_manual']); ?></small>
                        </div>
                      </div>
                      <p class="small text-muted mb-3"><i class="bi bi-calendar-check me-1"></i> <?php echo date('d/m/Y - H:i', strtotime($sol['fecha_hora'])); ?></p>
                      
                      <div class="d-flex gap-2">
                        <a href="../PHP/gestionar_clase.php?id=<?php echo $sol['id']; ?>&accion=aceptar" class="btn btn-success btn-sm w-100 rounded-pill fw-bold shadow-sm">Aceptar</a>
                        <a href="../PHP/gestionar_clase.php?id=<?php echo $sol['id']; ?>&accion=rechazar" class="btn btn-white btn-sm w-100 rounded-pill border text-danger small">Rechazar</a>
                      </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-check2-all text-success fs-1"></i>
                    <p class="text-muted small mt-2">¡Todo al día! No hay solicitudes pendientes.</p>
                </div>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>