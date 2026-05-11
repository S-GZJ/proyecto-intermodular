<?php
/*-- INICIO DE SESIÓN Y LÓGICA DE CONTROL --*/
session_start();

//Variable para el control de acceso en el Frontend
$esta_logueado = isset($_SESSION['usuario_id']) ? 'true' : 'false';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISIMatch - Aprende con los mejores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center gap-2" href="index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i>
          <span>ISIMatch</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
          <ul class="navbar-nav gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold text-primary-custom" href="#" onclick="verificarAccesoCatalogo(event)">Encontrar profesor</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="VIEWS/registro.php?rol=profesor">Convertirse en tutor</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#faq">Ayuda</a></li>
          </ul>
        </div>

        <div class="d-flex gap-2">
          <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="VIEWS/<?php echo ($_SESSION['rol'] == 'profesor') ? 'dashboard-profesor.php' : 'dashboard-alumno.php'; ?>" class="btn btn-primary-custom rounded-pill px-4">
              Mi Panel
            </a>
            <a href="PHP/logout.php" class="btn btn-outline-danger rounded-pill px-3">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          <?php else: ?>
            <a href="VIEWS/login.php" class="btn btn-outline-custom rounded-pill px-4">Entrar</a>
            <a href="VIEWS/registro.php" class="btn btn-primary-custom rounded-pill px-4">Registrarse</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <header class="container" style="margin-top: 140px; margin-bottom: 80px">
      <div class="row align-items-center">
        <div class="col-md-6">
          <br></br>
          <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">🚀 Aprende sin límites</span>
          <h1 class="display-4 fw-bold mb-4">
            Encuentra tu profesor <br /><span class="text-primary-custom">ideal hoy mismo.</span>
          </h1>
          <p class="lead text-muted mb-5">
            Conecta con profesores expertos verificados. Reserva clases online y mejora tus habilidades con nuestra plataforma segura e integrada.
          </p>

          <form action="VIEWS/catalogo.php" method="GET" class="bg-white p-2 rounded-pill shadow-lg d-flex gap-2 border">
            <div class="input-group">
              <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
              <input type="text" name="q" class="form-control border-0 shadow-none" placeholder="¿Qué quieres aprender?"/>
            </div>
            <button type="submit" onclick="verificarAccesoCatalogo(event)" class="btn btn-primary-custom px-5 rounded-pill fw-bold">BUSCAR</button>
          </form>
        </div>

        <div class="col-md-6 text-center mt-5 mt-md-0">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-lg" alt="Estudiantes" />
        </div>
      </div>
    </header>

    <footer class="py-4 mt-auto" style="background-color: #3bb3bd !important;">
      <div class="container text-center">
        <div class="mb-2">
          <span class="fw-bold text-white">ISIMatch</span> <span class="text-white">&copy; 2026</span>
        </div>
        <small class="text-white"> 
          <a href="./VIEWS/terminos.html" class="text-decoration-none text-white me-2">Términos y condiciones</a>
          |
          <a href="./VIEWS/privacidad.html" class="text-decoration-none text-white ms-2">Política de privacidad</a>
        </small>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      function verificarAccesoCatalogo(event) {
        const estaLogueado = <?php echo $esta_logueado; ?>;
        
        if (estaLogueado) {
            // Si el evento viene de un formulario, dejamos que el 'submit' siga su curso
            if (event.target.tagName !== 'BUTTON') {
                event.preventDefault();
                window.location.href = "VIEWS/catalogo.php";
            }
        } else {
            event.preventDefault();
            alert("🔒 Acceso restringido\n\nDebes iniciar sesión o crear una cuenta para ver el catálogo de profesores.");
            window.location.href = "VIEWS/registro.php";
        }
      }
    </script>
</body>
