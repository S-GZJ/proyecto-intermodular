<?php
session_start();

// 1. ESCUDO DE SEGURIDAD: Solo profesores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];

// 2. OBTENER DATOS ACTUALES PARA RELLENAR EL FORMULARIO
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, pd.titulo_profesional, pd.tarifa_hora, pd.bio 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$usuario_id'";

$resultado = $conn->query($sql);
$datos = $resultado->fetch_assoc();

$nombre_mostrar = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Profesor';
$letra_inicial = strtoupper(substr($nombre_mostrar, 0, 1));
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configuración - ISIMatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />

    <style>
      /* Estilos específicos para las pestañas de configuración */
      .nav-pills .nav-link {
        color: var(--text-dark);
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
      }
      .nav-pills .nav-link:hover {
        background-color: var(--bg-light);
      }
      .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: var(--primary-color) !important;
        color: white !important;
      }
    </style>
  </head>

  <body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i> ISIMatch
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDashboard">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarDashboard">
          <ul class="navbar-nav ms-auto align-items-center gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold text-dark" href="dashboard-profesor.php">
                <i class="bi bi-grid-fill"></i> Panel
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="dashboard-profesor.php"><i class="bi bi-calendar3"></i> Mi Calendario</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <img src="https://placehold.co/40x40/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle border" alt="Perfil" />
                <span><?php echo $nombre_mostrar; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item" href="ficha.php">Ver mi perfil</a></li>
                <li><a class="dropdown-item active" href="configuracion-profesor.php">Configuración</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <div class="mb-4">
        <h2 class="fw-bold text-dark">Configuración de la cuenta</h2>
        <p class="text-muted mb-0">Gestiona tus datos, tu perfil público y tus preferencias de cobro.</p>
      </div>

      <div class="row g-4">
        <div class="col-lg-3">
          <div class="card card-custom p-3 sticky-top" style="top: 100px;">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              <button class="nav-link active text-start mb-2 fw-bold" id="v-pills-personal-tab" data-bs-toggle="pill" data-bs-target="#v-pills-personal" type="button" role="tab">
                <i class="bi bi-person me-2"></i> Datos Personales
              </button>
              <button class="nav-link text-start mb-2 fw-bold" id="v-pills-profesional-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profesional" type="button" role="tab">
                <i class="bi bi-briefcase me-2"></i> Perfil Profesional
              </button>
              <button class="nav-link text-start mb-2 fw-bold" id="v-pills-seguridad-tab" data-bs-toggle="pill" data-bs-target="#v-pills-seguridad" type="button" role="tab">
                <i class="bi bi-shield-lock me-2"></i> Seguridad
              </button>
              <button class="nav-link text-start fw-bold" id="v-pills-cobros-tab" data-bs-toggle="pill" data-bs-target="#v-pills-cobros" type="button" role="tab">
                <i class="bi bi-bank me-2"></i> Datos de Cobro
              </button>
            </div>
          </div>
        </div>

        <div class="col-lg-9">
          <div class="tab-content" id="v-pills-tabContent">
            
            <div class="tab-pane fade show active" id="v-pills-personal" role="tabpanel">
              <div class="card card-custom p-4 mb-4">
                <h4 class="fw-bold mb-4">Información Básica</h4>
                
                <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                  <img src="https://placehold.co/100x100/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle shadow-sm" alt="Tu foto">
                  <div>
                    <h6 class="fw-bold">Foto de perfil</h6>
                    <p class="text-muted small mb-2">Se recomienda una imagen cuadrada, formato JPG o PNG.</p>
                    <button class="btn btn-outline-custom btn-sm"><i class="bi bi-upload"></i> Subir nueva foto</button>
                    <button class="btn btn-link text-danger btn-sm text-decoration-none">Eliminar</button>
                  </div>
                </div>

                <form>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">NOMBRE</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">APELLIDOS</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['apellidos']); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                      <input type="email" class="form-control" value="<?php echo htmlspecialchars($datos['email']); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                      <input type="tel" class="form-control" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>" placeholder="+34 600 000 000">
                    </div>
                    <div class="col-12 text-end mt-4">
                      <button type="button" class="btn btn-primary-custom">Guardar Cambios</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="tab-pane fade" id="v-pills-profesional" role="tabpanel">
              <div class="card card-custom p-4 mb-4">
                <h4 class="fw-bold mb-4">Tu Tarjeta de Presentación</h4>
                
                <div class="alert alert-info small mb-4">
                  <i class="bi bi-info-circle"></i> Esta es la información que verán los alumnos cuando busquen profesores en el catálogo.
                </div>

                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label small fw-bold text-muted">TÍTULO PROFESIONAL</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['titulo_profesional'] ?? ''); ?>" placeholder="Ej: Profesor Nativo de Inglés | Matemático Experto">
                      <div class="form-text">Resume lo que haces en una sola línea atractiva.</div>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="form-label small fw-bold text-muted">TARIFA POR HORA (€)</label>
                      <div class="input-group">
                        <input type="number" class="form-control" step="0.5" value="<?php echo htmlspecialchars($datos['tarifa_hora'] ?? '15'); ?>" min="5" max="100">
                        <span class="input-group-text bg-light">€/h</span>
                      </div>
                    </div>

                    <div class="col-12 mt-4">
                      <label class="form-label small fw-bold text-muted">SOBRE TUS CLASES (BIOGRAFÍA)</label>
                      <textarea class="form-control" rows="6" placeholder="Explica tu metodología, a quién van dirigidas tus clases y qué pueden esperar los alumnos de ti..."><?php echo htmlspecialchars($datos['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-12 text-end mt-4">
                      <button type="button" class="btn btn-primary-custom">Guardar Perfil</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="tab-pane fade" id="v-pills-seguridad" role="tabpanel">
              <div class="card card-custom p-4 mb-4">
                <h4 class="fw-bold mb-4">Cambiar Contraseña</h4>
                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label small fw-bold text-muted">CONTRASEÑA ACTUAL</label>
                      <input type="password" class="form-control" placeholder="Escribe tu contraseña actual">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">NUEVA CONTRASEÑA</label>
                      <input type="password" class="form-control" placeholder="Mínimo 8 caracteres">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted">REPETIR NUEVA CONTRASEÑA</label>
                      <input type="password" class="form-control" placeholder="Repite para confirmar">
                    </div>
                    <div class="col-12 text-end mt-4">
                      <button type="button" class="btn btn-primary-custom">Actualizar Contraseña</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="tab-pane fade" id="v-pills-cobros" role="tabpanel">
              <div class="card card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <h4 class="fw-bold mb-0">¿Dónde quieres recibir tu dinero?</h4>
                </div>
                
                <p class="text-muted mb-4">Añade la cuenta bancaria o cuenta de PayPal donde ISIMatch te transferirá el dinero de tus clases completadas.</p>

                <div class="p-3 border border-success border-2 rounded bg-light mb-4 position-relative">
                  <span class="badge bg-success position-absolute top-0 end-0 m-2"><i class="bi bi-check-circle"></i> Verificada</span>
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-bank fs-1 text-success"></i>
                    <div>
                      <p class="mb-0 fw-bold">IBAN finalizado en **** 4589</p>
                      <small class="text-muted">Titular: <?php echo htmlspecialchars($datos['nombre'] . ' ' . $datos['apellidos']); ?></small>
                    </div>
                  </div>
                </div>

                <button class="btn btn-outline-custom w-100 py-3 border-dashed fw-bold">
                  <i class="bi bi-plus-circle"></i> Cambiar método de cobro
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>