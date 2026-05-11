<?php
/*
 * ISIMatch - Catálogo de Profesores
 * Este archivo gestiona la búsqueda, filtrado y visualización de profesores
 */

//--LÓGICA DE SERVIDOR (PHP)--

//Iniciamos la sesión de forma segura para identificar al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Conexión a la base de datos (localhost o servidor)
include '../PHP/conexion.php'; 

//Verificamos si el usuario ha iniciado sesión
$esta_logueado = isset($_SESSION['usuario_id']);

//Si está logueado, obtenemos sus datos; si no, valores por defecto para evitar errores
$rol_usuario = $esta_logueado ? $_SESSION['rol'] : '';
$nombre_usuario = $esta_logueado ? htmlspecialchars($_SESSION['nombre']) : '';
$letra_usuario = $esta_logueado ? strtoupper(substr($nombre_usuario, 0, 1)) : 'U';

//Definimos rutas dinámicas: el menú enviará al usuario a un sitio u otro según su rol
$link_panel = ($rol_usuario == 'profesor') ? 'dashboard-profesor.php' : 'dashboard-alumno.php';
$link_perfil_propio = ($rol_usuario == 'profesor') ? 'ficha-profesor.php' : 'ficha-alumno.php';

//--PROCESAMIENTO DE FILTROS--

// Recogemos lo que el usuario escribe en el buscador y lo limpiamos contra ataques SQL (Inyección)
$busqueda = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

//Recogemos el precio máximo del filtro deslizante (por defecto 100€)
$precio_max = isset($_GET['precio']) ? (int)$_GET['precio'] : 100;

//Consulta SQL Base: Unimos la tabla de usuarios con la de detalles profesionales
//Solo buscamos usuarios que tengan el rol de 'profesor'
$sql_base = "SELECT u.id, u.nombre, u.apellidos, pd.titulo_profesional, pd.tarifa_hora, pd.bio, pd.valoracion_media 
             FROM usuarios u 
             LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
             WHERE u.rol = 'profesor'";

//Si el usuario escribió algo en el buscador, añadimos condiciones a la consulta
if (!empty($busqueda)) {
    $sql_base .= " AND (u.nombre LIKE '%$busqueda%' OR u.apellidos LIKE '%$busqueda%' OR pd.titulo_profesional LIKE '%$busqueda%')";
}

//Aplicamos siempre el filtro de precio máximo
$sql_base .= " AND pd.tarifa_hora <= $precio_max";

//Ordenamos para que los profesores mejor valorados aparezcan primero
$sql_base .= " ORDER BY pd.valoracion_media DESC, u.id DESC";

//Ejecutamos la consulta en la base de datos
$res = $conn->query($sql_base);

//Si el usuario está logueado, contamos sus mensajes sin leer para mostrar la notificación (punto rojo)
$mensajes_nuevos = 0;
if($esta_logueado) {
    $mi_id = $_SESSION['usuario_id'];
    $msj_res = $conn->query("SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = '$mi_id' AND leido = 0");
    $msj_data = $msj_res->fetch_assoc();
    $mensajes_nuevos = $msj_data['total'];
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo de Profesores - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css"/>
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="../index.php">ISIMatch</a>
        
        <div class="collapse navbar-collapse justify-content-center">
          <ul class="navbar-nav gap-3">
            <li class="nav-item">
                <a class="nav-link fw-bold text-primary-custom" href="catalogo.php">Encontrar profesor</a>
            </li>
          </ul>
        </div>

        <div class="d-flex align-items-center gap-3">
          <?php if($esta_logueado): ?>
            <a href="mensajes.php" class="text-dark position-relative me-2">
                <i class="bi bi-chat-dots fs-5"></i>
                <?php if($mensajes_nuevos > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        <?php echo $mensajes_nuevos; ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                    <?php echo $letra_usuario; ?>
                </div>
                <span class="fw-bold d-none d-md-inline"><?php echo $nombre_usuario; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3">
                <li><a class="dropdown-item py-2" href="<?php echo $link_panel; ?>"><i class="bi bi-grid-fill me-2"></i>Mi Panel</a></li>
                <li><a class="dropdown-item py-2" href="<?php echo $link_perfil_propio; ?>"><i class="bi bi-person-fill me-2"></i>Mi Perfil</a></li>
                
                <?php if($rol_usuario == 'alumno'): ?>
                    <li><a class="dropdown-item py-2" href="pagos-alumno.php"><i class="bi bi-credit-card-fill me-2"></i>Pagos y Facturas</a></li>
                <?php endif; ?>

                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger py-2" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-custom btn-sm rounded-pill px-3">Entrar</a>
            <a href="registro.php" class="btn btn-primary-custom btn-sm rounded-pill px-3">Registrarse</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <div class="container" style="margin-top: 120px; margin-bottom: 50px;">
      
      <div class="row mb-5 align-items-end g-3">
          <div class="col-lg-4">
              <h2 class="fw-bold mb-0">Nuestros Profesores</h2>
              <p class="text-muted small">Más de 200 profesionales listos para ayudarte.</p>
          </div>
          <div class="col-lg-8">
              <form action="catalogo.php" method="GET" class="card p-3 border-0 shadow-sm rounded-4">
                  <div class="row g-3 align-items-center">
                      <div class="col-md-5">
                          <div class="input-group bg-light rounded-pill px-2">
                              <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                              <input type="text" name="q" class="form-control bg-transparent border-0" placeholder="Materia, nombre o título..." value="<?php echo htmlspecialchars($busqueda); ?>">
                          </div>
                      </div>
                      <div class="col-md-4">
                          <label class="form-label small fw-bold mb-0 text-muted">Precio máx: <span id="priceLabel"><?php echo $precio_max; ?></span>€/h</label>
                          <input type="range" name="precio" class="form-range custom-range" min="5" max="100" step="5" value="<?php echo $precio_max; ?>" oninput="document.getElementById('priceLabel').innerText = this.value">
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary-custom w-100 rounded-pill fw-bold shadow-sm">Actualizar filtros</button>
                      </div>
                  </div>
              </form>
          </div>
      </div>

      <div class="row g-4">
        <?php
        //Si la consulta devuelve resultados, recorremos cada profesor
        if ($res && $res->num_rows > 0) {
            while($profe = $res->fetch_assoc()) {
                //Preparamos los datos de cada tarjeta
                $inicial = strtoupper(substr($profe['nombre'], 0, 1));
                $tarifa = number_format($profe['tarifa_hora'] ?? 15, 0);
                $rating = $profe['valoracion_media'] ?? '5.0';
                ?>
                <div class="col-md-6 col-lg-4">
                  <div class="card card-custom h-100 p-4 border-0 shadow-sm rounded-4 position-relative transition-hover">
                    
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1 shadow-sm">
                            <i class="bi bi-star-fill me-1"></i> <?php echo $rating; ?>
                        </span>
                    </div>

                    <div class="d-flex gap-3 align-items-center mb-3">
                      <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:60px; height:60px; font-size: 1.4rem;">
                        <?php echo $inicial; ?>
                      </div>
                      <div class="overflow-hidden">
                        <h5 class="mb-0 fw-bold text-truncate"><?php echo htmlspecialchars($profe['nombre'] . " " . $profe['apellidos']); ?></h5>
                        <small class="text-primary-custom fw-bold text-truncate d-block">
                            <?php echo htmlspecialchars($profe['titulo_profesional'] ?? 'Tutor ISIMatch'); ?>
                        </small>
                      </div>
                    </div>
                    
                    <p class="text-muted small mb-4">
                        <?php 
                        $bio_corta = $profe['bio'] ?? 'Este profesor está terminando de configurar su biografía para darte el mejor servicio.';
                        echo htmlspecialchars(substr($bio_corta, 0, 110)) . '...'; 
                        ?>
                    </p>
                    
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                      <div>
                          <span class="fw-bold fs-4"><?php echo $tarifa; ?>€</span><small class="text-muted">/h</small>
                      </div>
                      <div class="d-flex gap-2">
                          <a href="mensajes.php?con=<?php echo $profe['id']; ?>" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Enviar mensaje">
                              <i class="bi bi-chat-left-text"></i>
                          </a>
                          <a href="ficha-profesor.php?id=<?php echo $profe['id']; ?>" class="btn btn-outline-custom btn-sm rounded-pill px-3 fw-bold">Ver Perfil</a>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
            }
        } else {
            //Si la búsqueda no encuentra resultados, mostramos este mensaje
            echo "
            <div class='col-12 text-center py-5'>
                <div class='mb-4'>
                    <i class='bi bi-emoji-frown text-muted' style='font-size: 4rem;'></i>
                </div>
                <h4 class='fw-bold text-dark'>No hay coincidencias exactas</h4>
                <p class='text-muted'>Intenta ampliar el rango de precio o buscar una materia más general.</p>
                <a href='catalogo.php' class='btn btn-primary-custom rounded-pill px-4 mt-2 shadow-sm'>Restablecer catálogo</a>
            </div>";
        }
        ?>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>