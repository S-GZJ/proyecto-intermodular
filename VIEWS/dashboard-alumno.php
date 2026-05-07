<?php
//Iniciamos o recuperamos la sesión del usuario
session_start(); 

/*--ESCUDO DE SEGURIDAD--
Verificamos dos condiciones:
-Que exista una sesión activa (usuario_id)
-Que el rol sea estrictamente 'alumno'
Esto evita que profesores o personas no logueadas entren a esta área privada
*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    //Redirección forzosa al login si no cumple los requisitos
    header("Location: login.php");
    exit();
}

//Preparamos el nombre para mostrarlo en el saludo personalizado
$nombre_alumno = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Alumno';
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Aprendizaje - ISIMatch</title>

    <!--Framework de diseño Bootstrap y set de iconos oficial-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
  </head>

  <body class="bg-light">
    <!--BARRA DE NAVEGACIÓN (Navbar)-->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center gap-2" href="index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i>
          <span>ISIMatch</span>
        </a>

        <!--Botón para colapsar el menú en dispositivos móviles-->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDashboard">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarDashboard">
          <ul class="navbar-nav ms-auto align-items-center gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold text-primary-custom" href="dashboard-alumno.php">
                <i class="bi bi-journal-bookmark-fill"></i> Mis clases
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="catalogo.php">
                <i class="bi bi-search"></i> Buscar profesor
              </a>
            </li>

            <!--DROPDOWN DE MENSAJES: Simulación de notificaciones pendientes-->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-chat-dots"></i> Mensajes
                <span class="badge bg-danger rounded-pill">2</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="width: 320px">
                <li><h6 class="dropdown-header fw-bold">Mensajes de Profesores</h6></li>
                <!--Ejemplo de mensaje interactivo-->
                <li>
                  <a class="dropdown-item d-flex align-items-center gap-3 py-2" href="#">
                    <img src="https://placehold.co/30x30" class="rounded-circle" alt="Isaac" />
                    <div class="overflow-hidden">
                      <div class="d-flex justify-content-between small">
                        <strong class="text-dark">Isaac Ruiz</strong>
                        <span class="text-muted" style="font-size: 0.75rem">Hace 5 min</span>
                      </div>
                      <div class="text-muted text-truncate small">Here is the link for the exercise...</div>
                    </div>
                  </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-center text-primary-custom fw-bold small py-2" href="#">Ir a la bandeja de entrada</a></li>
              </ul>
            </li>

            <!--MENÚ DE USUARIO: Acceso al perfil personal y cierre de sesión-->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <img src="https://placehold.co/40x40/FFC947/white?text=A" class="rounded-circle border" alt="Perfil" />
                <span><?php echo $nombre_alumno; ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item" href="ficha-alumno.php">Mi perfil</a></li>
                <li><a class="dropdown-item" href="pagos-alumno.php">Pagos y facturación</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container my-5">
      <!--ENCABEZADO: Saludo dinámico y botón de acción principal-->
      <div class="row align-items-center mb-5">
        <div class="col-md-8">
          <h2 class="fw-bold text-dark">¡Hola, <?php echo $nombre_alumno; ?>!</h2>
          <p class="text-muted">Tienes <strong>1 clase confirmada</strong> próximamente.</p>
        </div>
        <div class="col-md-4 text-end">
          <a href="catalogo.php" class="btn btn-primary-custom shadow-sm">
            <i class="bi bi-plus-lg"></i> Reservar nueva clase
          </a>
        </div>
      </div>

      <div class="row g-4">
        <!--SECCIÓN PRINCIPAL: Clases próximas y Aula Virtual-->
        <div class="col-lg-8">
          <div class="card card-custom p-4 mb-5 border-start border-4 border-primary">
            <div class="row align-items-center">
              <div class="col-md-8">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2 fw-bold">EN 15 MINUTOS</span>
                <h3 class="fw-bold mb-1">Inglés Conversacional (B2)</h3>
                <div class="d-flex align-items-center gap-2 text-muted mt-2">
                  <img src="https://placehold.co/30x30" class="rounded-circle" alt="Profe" />
                  <span>con <strong>Isaac Ruiz</strong></span>
                </div>
                <p class="mt-3 text-muted mb-0"><i class="bi bi-camera-video"></i> La sala se abrirá 5 minutos antes.</p>
              </div>
              <div class="col-md-4 text-end mt-3 mt-md-0">
                <!--Botón con animación 'pulse' para captar la atención del alumno-->
                <a href="videollamada.php" class="btn btn-primary-custom w-100 py-3 fw-bold pulse-animation">ENTRAR AHORA</a>
              </div>
            </div>
          </div>

          <h4 class="fw-bold mb-3">Mis Reservas</h4>

          <!--TARJETA DE RESERVA PENDIENTE: Muestra el estado de la solicitud-->
          <div class="card card-custom p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex gap-3 align-items-center">
                <div class="text-center bg-light rounded p-2" style="width: 60px">
                  <span class="d-block fw-bold fs-5">15</span>
                  <small class="text-uppercase">DIC</small>
                </div>
                <div>
                  <h6 class="fw-bold mb-0">Matemáticas: Cálculo</h6>
                  <small class="text-muted">Prof. Silvia P. • 17:00 - 18:00</small>
                </div>
              </div>
              <div class="text-end">
                <span class="badge bg-warning text-dark mb-2">Pendiente de Aceptación</span>
                <div>
                  <button class="btn btn-link text-danger text-decoration-none btn-sm p-0" onclick="cancelarReserva(this)">Cancelar</button>
                </div>
              </div>
            </div>
          </div>

          <!--HISTORIAL: Clases pasadas con opción a valorar al profesor-->
          <div class="card card-custom p-3 mb-3 bg-light border-0 opacity-75">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex gap-3 align-items-center">
                <div class="text-center bg-white rounded p-2" style="width: 60px">
                  <span class="d-block fw-bold fs-5 text-muted">10</span>
                  <small class="text-uppercase text-muted">DIC</small>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-muted">Programación Web</h6>
                  <small class="text-muted">Prof. Marcos Gómez • Finalizada</small>
                </div>
              </div>
              <div>
                <button class="btn btn-outline-warning btn-sm border-2 fw-bold text-dark">
                  <i class="bi bi-star-fill"></i> Valorar Clase
                </button>
              </div>
            </div>
          </div>
        </div>

        <!--BARRA LATERAL (Aside): Ayuda rápida con posicionamiento 'sticky'-->
        <div class="col-lg-4">
          <div class="card card-custom p-4 text-center sticky-top" style="top: 100px">
            <div class="mb-3">
              <i class="bi bi-lightning-charge-fill text-warning display-4"></i>
            </div>
            <h5>¿Necesitas ayuda urgente?</h5>
            <p class="text-muted small">Encuentra profesores con disponibilidad inmediata para hoy.</p>
            <a href="catalogo.php" class="btn btn-outline-custom w-100">Ver Profesores Online</a>
          </div>
        </div>
      </div>
    </div>

    <!--SCRIPTS: Bootstrap JS y funciones personalizadas de la interfaz-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      /*Función para cancelar una reserva:
       -Utiliza animaciones de CSS y manipulación del DOM para mejorar la experiencia de usuario
       */
      function cancelarReserva(boton) {
        if (confirm("¿Seguro que quieres cancelar esta solicitud de clase?")) {
          const tarjeta = boton.closest(".card");
          tarjeta.style.transition = "all 0.5s ease";
          tarjeta.style.opacity = "0";
          tarjeta.style.transform = "translateX(-20px)";

          //Esperamos a que termine la animación antes de quitar el elemento
          setTimeout(() => {
            tarjeta.remove();
          }, 500);
        }
      }
    </script>

    <style>
      /*Animación personalizada para el botón de 'Entrar a Clase'*/
      @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(59, 179, 189, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(59, 179, 189, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 179, 189, 0); }
      }
      .pulse-animation {
        animation: pulse 2s infinite;
      }
    </style>
  </body>
</html>