<?php
//Iniciamos la sesión para reconocer al usuario que ha entrado
session_start();

/*--ESCUDO DE SEGURIDAD--
Verificamos que el usuario esté logueado y que sea un 'alumno'
Si un profesor o alguien sin sesión intenta entrar, lo mandamos al login
*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    // RUTA MANTENIDA: login.php está en la misma carpeta VIEWS
    header("Location: login.php");
    exit();
}

//--CONEXIÓN A LA BASE DE DATOS--
// RUTA CORREGIDA: Salimos de VIEWS y entramos en PHP
include '../PHP/conexion.php';

$usuario_id = $_SESSION['usuario_id'];

/*--CONSULTA SQL CON JOIN--
Seleccionamos datos de la tabla 'usuarios' (u) y 'alumnos_detalles' (ad)
Usamos LEFT JOIN para que la página cargue incluso si el alumno aún no ha
rellenado sus objetivos de aprendizaje en la tabla secundaria
*/
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, u.fecha_registro, ad.objetivos_aprendizaje 
        FROM usuarios u 
        LEFT JOIN alumnos_detalles ad ON u.id = ad.usuario_id 
        WHERE u.id = '$usuario_id'";

$resultado = $conn->query($sql);
$datos_usuario = $resultado->fetch_assoc();

/*--FORMATEO DE FECHAS (Localización)--
PHP por defecto devuelve los meses en inglés. Creamos un array asociativo
para traducir el nombre del mes y que el perfil se vea más profesional en español
 */
$meses = [
    "January"=>"Enero", "February"=>"Febrero", "March"=>"Marzo", "April"=>"Abril", 
    "May"=>"Mayo", "June"=>"Junio", "July"=>"Julio", "August"=>"Agosto", 
    "September"=>"Septiembre", "October"=>"Octubre", "November"=>"Noviembre", "December"=>"Diciembre"
];
$mes_ingles = date("F", strtotime($datos_usuario['fecha_registro']));
$anio = date("Y", strtotime($datos_usuario['fecha_registro']));
$mes_espanol = $meses[$mes_ingles];

//Extraemos la inicial del nombre para el avatar circular
$letra_inicial = strtoupper(substr($datos_usuario['nombre'], 0, 1)); 
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Perfil - ISIMatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    
    <link rel="stylesheet" href="../CSS/style.css" />
  </head>

  <body class="bg-light">
    <nav class="navbar navbar-light bg-white border-bottom sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard-alumno.php">
          <i class="bi bi-arrow-left-circle text-muted"></i>
          <span>Volver a mis clases</span>
        </a>

        <div class="d-flex align-items-center gap-2">
          <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
              <span class="text-muted small d-none d-md-block">Estás editando tu perfil</span>
              <img src="https://placehold.co/40x40/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle border" alt="Perfil" />
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
              <li><a class="dropdown-item active" href="#">Mi perfil</a></li>
              <li><a class="dropdown-item" href="pagos-alumno.php">Pagos y facturación</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item text-danger" href="../PHP/logout.php">Cerrar sesión</a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <div class="container my-5">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card card-custom p-4 text-center mb-4 border-0 shadow-sm">
            <div class="position-relative d-inline-block mx-auto mb-3">
              <img src="https://placehold.co/120x120/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle border border-4 border-white shadow-sm" />
              <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border shadow-sm"><i class="bi bi-camera-fill"></i></button>
            </div>
            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($datos_usuario['nombre'] . ' ' . $datos_usuario['apellidos']); ?></h4>
            <p class="text-muted mb-3">Estudiante</p>
            <p class="small text-muted"><i class="bi bi-calendar3"></i> Miembro desde <?php echo $mes_espanol . " " . $anio; ?></p>

            <hr class="my-4 opacity-10" />

            <div class="row text-center">
              <div class="col-6 border-end">
                <h5 class="fw-bold mb-0">0</h5>
                <small class="text-muted">Clases</small>
              </div>
              <div class="col-6">
                <h5 class="fw-bold mb-0">-</h5>
                <small class="text-muted">Nota media</small>
              </div>
            </div>
          </div>

          <div class="card card-custom p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Tu Progreso</h5>
            <div class="mb-3">
              <div class="d-flex justify-content-between small mb-1">
                <span>Horas de Matemáticas</span>
                <span class="fw-bold">0h</span>
              </div>
              <div class="progress" style="height: 6px">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card card-custom p-5 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="fw-bold mb-0">Información Personal</h4>
              <button class="btn btn-outline-custom btn-sm"><i class="bi bi-pencil"></i> Editar datos</button>
            </div>

            <form>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small text-muted fw-bold">NOMBRE</label>
                  <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label small text-muted fw-bold">APELLIDOS</label>
                  <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($datos_usuario['apellidos']); ?>" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label small text-muted fw-bold">CORREO ELECTRÓNICO</label>
                  <input type="email" class="form-control bg-white" value="<?php echo htmlspecialchars($datos_usuario['email']); ?>" readonly />
                </div>
                <div class="col-md-6">
                  <label class="form-label small text-muted fw-bold">TELÉFONO</label>
                  <input type="tel" class="form-control bg-white" value="<?php echo htmlspecialchars($datos_usuario['telefono'] ?? 'No especificado'); ?>" readonly />
                </div>
                <div class="col-12">
                  <label class="form-label small text-muted fw-bold">OBJETIVOS DE APRENDIZAJE</label>
                  <textarea class="form-control bg-white" rows="3" readonly><?php echo htmlspecialchars($datos_usuario['objetivos_aprendizaje'] ?? 'Aún no has definido tus objetivos de aprendizaje.'); ?></textarea>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>