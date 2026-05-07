<?php
session_start();

/*--ESCUDO DE SEGURIDAD--
Verificamos que el usuario esté logueado Y que su rol sea 'profesor'
Si un alumno intenta entrar por URL, será redirigido al login
*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];

/*--CARGA DE DATOS PARA EL FORMULARIO (Read)--
Realizamos un SELECT combinando 'usuarios' y 'profesores_detalles'
Usamos LEFT JOIN para que, si el profesor es nuevo y aún no tiene biografía 
o tarifa, la página cargue igual y le permita crearlos
*/
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, pd.titulo_profesional, pd.tarifa_hora, pd.bio 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$usuario_id'";

$resultado = $conn->query($sql);
$datos = $resultado->fetch_assoc();

//Datos para el avatar y nombre en la navegación
$nombre_mostrar = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Profesor';
$letra_inicial = strtoupper(substr($nombre_mostrar, 0, 1));
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configuración - ISIMatch</title>

    <!--Bootstrap y estilos-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />

    <style>
      /*Estilización de los Nav-Pills (pestañas laterales)*/
      .nav-pills .nav-link {
        color: var(--text-dark);
        border-radius: 8px;
        padding: 12px 20px;
        transition: all 0.3s ease;
      }
      /*Cambio de color al estar activa la pestaña*/
      .nav-pills .nav-link.active {
        background-color: var(--primary-color) !important;
        color: white !important;
      }
    </style>
  </head>

  <body class="bg-light">
    <!--NAVEGACIÓN-->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i> ISIMatch
        </a>
        <!--Menú de usuario con dropdown para ver perfil o cerrar sesión-->
        <div class="collapse navbar-collapse" id="navbarDashboard">
          <ul class="navbar-nav ms-auto align-items-center gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold text-dark" href="dashboard-profesor.php"><i class="bi bi-grid-fill"></i> Panel</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <img src="https://placehold.co/40x40/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle border">
                <span><?php echo $nombre_mostrar; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item" href="ficha-profesor.php">Ver mi perfil</a></li>
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
        <h2 class="fw-bold text-dark">Configuración</h2>
        <p class="text-muted">Gestiona tus datos personales y tu oferta profesional.</p>
      </div>

      <div class="row g-4">
        <!--COLUMNA IZQUIERDA: MENÚ DE PESTAÑAS (Nav-Pills)-->
        <div class="col-lg-3">
          <div class="card card-custom p-3 sticky-top" style="top: 100px;">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
              <!--Estas pestañas funcionan con Atributos Data de Bootstrap sin recargar la página-->
              <button class="nav-link active text-start mb-2 fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-personal" type="button">
                <i class="bi bi-person me-2"></i> Datos Personales
              </button>
              <button class="nav-link text-start mb-2 fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-profesional" type="button">
                <i class="bi bi-briefcase me-2"></i> Perfil Profesional
              </button>
              <button class="nav-link text-start mb-2 fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-seguridad" type="button">
                <i class="bi bi-shield-lock me-2"></i> Seguridad
              </button>
            </div>
          </div>
        </div>

        <!--COLUMNA DERECHA, CONTENIDO DE LAS PESTAÑAS-->
        <div class="col-lg-9">
          <div class="tab-content" id="v-pills-tabContent">
            
            <!--PESTAÑA 1: DATOS PERSONALES-->
            <div class="tab-pane fade show active" id="v-pills-personal" role="tabpanel">
              <div class="card card-custom p-4 shadow-sm border-0">
                <h4 class="fw-bold mb-4">Información Básica</h4>
                <form>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">NOMBRE</label>
                      <!--Se usa value="" con PHP para rellenar el campo con lo que ya hay en la BDD-->
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">APELLIDOS</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['apellidos']); ?>">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">CORREO</label>
                      <input type="email" class="form-control" value="<?php echo htmlspecialchars($datos['email']); ?>">
                    </div>
                    <div class="col-12 text-end mt-4">
                      <button type="button" class="btn btn-primary-custom px-4">Actualizar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!--PESTAÑA 2: PERFIL PROFESIONAL (El que sale en el catálogo)-->
            <div class="tab-pane fade" id="v-pills-profesional" role="tabpanel">
              <div class="card card-custom p-4 shadow-sm border-0">
                <h4 class="fw-bold mb-4">Perfil Público</h4>
                <form>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label small fw-bold">TÍTULO PROFESIONAL</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['titulo_profesional'] ?? ''); ?>" placeholder="Ej: Especialista en Python">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label small fw-bold">TARIFA POR HORA (€)</label>
                      <input type="number" class="form-control" value="<?php echo htmlspecialchars($datos['tarifa_hora'] ?? '15'); ?>">
                    </div>
                    <div class="col-12">
                      <label class="form-label small fw-bold">BIOGRAFÍA</label>
                      <!--El texto de la biografía va dentro de las etiquetas textarea-->
                      <textarea class="form-control" rows="5"><?php echo htmlspecialchars($datos['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end mt-4">
                      <button type="button" class="btn btn-primary-custom px-4">Guardar Perfil</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!--PESTAÑA 3: SEGURIDAD (Cambio de contraseña)-->
            <div class="tab-pane fade" id="v-pills-seguridad" role="tabpanel">
              <div class="card card-custom p-4 shadow-sm border-0">
                <h4 class="fw-bold mb-4">Seguridad</h4>
                <div class="alert alert-warning small">Recuerda usar contraseñas seguras de más de 8 caracteres.</div>
                <form>
                  <label class="form-label small fw-bold">NUEVA CONTRASEÑA</label>
                  <input type="password" class="form-control mb-3">
                  <label class="form-label small fw-bold">REPETIR CONTRASEÑA</label>
                  <input type="password" class="form-control mb-4">
                  <button type="button" class="btn btn-primary-custom">Actualizar Contraseña</button>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <!--JS de Bootstrap necesario para que las pestañas (Tabs) cambien al hacer clic-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>