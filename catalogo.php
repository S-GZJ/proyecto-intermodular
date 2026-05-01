<?php
session_start();
include 'conexion.php'; 

$esta_logueado = isset($_SESSION['usuario_id']);
$rol_usuario = $esta_logueado ? $_SESSION['rol'] : '';
$nombre_usuario = $esta_logueado ? htmlspecialchars($_SESSION['nombre']) : '';
$letra_usuario = $esta_logueado ? strtoupper(substr($nombre_usuario, 0, 1)) : 'U';

// Enlaces del menú superior
$link_panel = ($rol_usuario == 'profesor') ? 'dashboard-profesor.php' : 'dashboard-alumno.php';
$link_perfil_propio = ($rol_usuario == 'profesor') ? 'ficha-profesor.php' : 'ficha-alumno.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Catálogo de Profesores - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">ISIMatch</a>
        <div class="collapse navbar-collapse justify-content-center">
          <ul class="navbar-nav gap-3">
            <li class="nav-item"><a class="nav-link fw-bold text-primary-custom" href="catalogo.php">Encontrar profesor</a></li>
          </ul>
        </div>
        <div class="d-flex align-items-center gap-2">
          <?php if($esta_logueado): ?>
            <div class="dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <img src="https://placehold.co/40x40/FFC947/white?text=<?php echo $letra_usuario; ?>" class="rounded-circle border" />
                <span class="fw-bold"><?php echo $nombre_usuario; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item" href="<?php echo $link_panel; ?>">Mi Panel</a></li>
                <li><a class="dropdown-item" href="<?php echo $link_perfil_propio; ?>">Mi Perfil</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-custom btn-sm">Entrar</a>
            <a href="registro.php" class="btn btn-primary-custom btn-sm">Registrarse</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <div class="container" style="margin-top: 120px; margin-bottom: 50px;">
      
      <!-- BOTÓN VOLVER AL DASHBOARD -->
      <div class="row mb-4">
          <div class="col-12">
              <a href="dashboard-alumno.php" class="btn btn-white shadow-sm border rounded-pill px-4 text-muted fw-bold">
                  <i class="bi bi-arrow-left me-2"></i> Volver a mi Panel
              </a>
          </div>
      </div>

      <div class="row g-4">
        <?php
        $sql_profes = "SELECT u.id, u.nombre, u.apellidos, pd.titulo_profesional, pd.tarifa_hora, pd.bio 
                       FROM usuarios u 
                       LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
                       WHERE u.rol = 'profesor' ORDER BY u.id DESC";
        $res = $conn->query($sql_profes);

        if ($res && $res->num_rows > 0) {
            while($profe = $res->fetch_assoc()) {
                $inicial = strtoupper(substr($profe['nombre'], 0, 1));
                $titulo = $profe['titulo_profesional'] ?? 'Nuevo Profesor';
                $tarifa = isset($profe['tarifa_hora']) ? number_format($profe['tarifa_hora'], 0) : '15';
                ?>
                <div class="col-md-6 col-lg-4">
                  <div class="card card-custom h-100 p-4 border-0 shadow-sm">
                    <div class="d-flex gap-3 align-items-center mb-3">
                      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width:60px; height:60px;"><?php echo $inicial; ?></div>
                      <div>
                        <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($profe['nombre'] . " " . $profe['apellidos']); ?></h5>
                        <small class="text-primary-custom fw-bold"><?php echo htmlspecialchars($titulo); ?></small>
                      </div>
                    </div>
                    <p class="text-muted small"><?php echo htmlspecialchars(substr($profe['bio'] ?? 'Sin biografía.', 0, 100)) . '...'; ?></p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                      <span class="fw-bold fs-4"><?php echo $tarifa; ?>€<small class="fs-6 text-muted">/h</small></span>
                      <a href="ficha-profesor.php?id=<?php echo $profe['id']; ?>" class="btn btn-outline-custom btn-sm rounded-pill">Ver Perfil</a>
                    </div>
                  </div>
                </div>
                <?php
            }
        }
        ?>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>