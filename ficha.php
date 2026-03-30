<?php
session_start();

// 1. ESCUDO DE SEGURIDAD: Comprobamos si es un profesor logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

// 2. CONEXIÓN A LA BASE DE DATOS
include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// 3. CONSULTA: Buscamos los datos de este usuario específico en ambas tablas
$sql = "SELECT u.nombre, u.apellidos, pd.titulo_profesional, pd.bio, pd.tarifa_hora, pd.valoracion_media, pd.total_resenas 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$usuario_id'";

$resultado = $conn->query($sql);
$datos_profesor = $resultado->fetch_assoc();

// 4. PREPARAMOS LAS VARIABLES (Controlando si están vacías al ser un usuario nuevo)
$nombre_completo = htmlspecialchars($datos_profesor['nombre'] . ' ' . $datos_profesor['apellidos']);
$titulo = htmlspecialchars($datos_profesor['titulo_profesional'] ?? 'Profesor en ISIMatch');
$bio = htmlspecialchars($datos_profesor['bio'] ?? 'Aún no has escrito una descripción sobre tus clases. ¡Añádela para atraer a más alumnos!');
$tarifa = isset($datos_profesor['tarifa_hora']) ? number_format($datos_profesor['tarifa_hora'], 2) : '15.00'; // Por defecto 15.00
$valoracion = isset($datos_profesor['valoracion_media']) ? number_format($datos_profesor['valoracion_media'], 1) : '0.0';
$resenas = isset($datos_profesor['total_resenas']) ? $datos_profesor['total_resenas'] : '0';
$letra_inicial = strtoupper(substr($datos_profesor['nombre'], 0, 1)); // Para el avatar
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil de <?php echo htmlspecialchars($datos_profesor['nombre']); ?> - ISIMatch</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />

    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <nav class="navbar navbar-light bg-white border-bottom sticky-top">
      <div class="container">
        <a
          class="navbar-brand fw-bold d-flex align-items-center gap-2"
          href="dashboard-profesor.php"
        >
          <i class="bi bi-arrow-left-circle text-muted"></i>
          <span>Volver al Panel</span>
        </a>

        <span class="badge bg-light text-dark border">
          <i class="bi bi-eye"></i> Vista pública de tu perfil
        </span>
      </div>
    </nav>

    <div class="container my-5">
      <div class="row">
        <div class="col-lg-8">
          <div class="card card-custom p-4 mb-4">
            <div class="d-flex gap-4 align-items-start">
              <div class="position-relative">
                <img
                  src="https://placehold.co/150x150/FFC947/white?text=<?php echo $letra_inicial; ?>"
                  class="rounded-circle shadow-sm"
                  alt="Profesor"
                />
                <button
                  class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border shadow-sm"
                >
                  <i class="bi bi-camera-fill"></i>
                </button>
              </div>

              <div class="w-100">
                <div class="d-flex justify-content-between">
                  <div>
                    <h2 class="mb-1"><?php echo $nombre_completo; ?></h2>
                    <p class="text-primary-custom fw-bold mb-2">
                      <?php echo $titulo; ?>
                    </p>
                  </div>

                  <div class="text-end">
                    <div class="d-flex align-items-center gap-1 text-warning">
                      <i class="bi bi-star-fill"></i>
                      <span class="fw-bold text-dark fs-5"><?php echo $valoracion; ?></span>
                      <span class="text-muted small">(<?php echo $resenas; ?> reseñas)</span>
                    </div>
                  </div>
                </div>

                <div class="mt-4">
                  <span class="badge bg-light text-dark border me-2">
                    Español (Nativo)
                  </span>
                  <span class="badge bg-light text-dark border me-2">
                    Inglés (B2)
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-custom p-4">
            <div class="d-flex justify-content-between mb-3">
              <h5 class="mb-0">Sobre la clase</h5>
              <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i> Editar texto
              </button>
            </div>

            <p class="text-muted">
              <?php echo nl2br($bio); ?>
            </p>
          </div>
        </div>

        <div class="col-lg-4">
          <div
            class="card card-custom p-4 sticky-top"
            style="top: 100px; border: 1px solid #eef0f2 !important"
          >
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h3 class="mb-0">
                <?php echo str_replace('.', ',', $tarifa); ?>€ <span class="fs-6 text-muted fw-normal">/ hora</span>
              </h3>
              <button class="btn btn-sm btn-link text-muted">
                <i class="bi bi-pencil"></i>
              </button>
            </div>

            <div class="alert alert-info small mb-4">
              <i class="bi bi-info-circle-fill"></i> Así ven los alumnos tu
              tarjeta de reserva.
            </div>

            <div class="d-grid gap-3">
              <button
                class="btn btn-outline-secondary py-3 fs-5 shadow-sm text-center"
              >
                <i class="bi bi-pencil-square"></i> Editar Información
              </button>

              <a
                href="dashboard-profesor.php"
                class="btn btn-primary-custom py-2 text-decoration-none text-center"
              >
                <i class="bi bi-calendar-check"></i> Gestionar Disponibilidad
              </a>
            </div>

            <hr class="my-4 opacity-10" />

            <div class="small text-muted text-center">
              <i class="bi bi-eye-fill"></i> Tu perfil es visible públicamente
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>