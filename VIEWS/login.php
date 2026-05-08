<?php
// --- GESTIÓN DE SESIÓN Y CONEXIÓN ---
session_start(); 

// Si el usuario ya está logueado, lo mandamos a su panel correspondiente directamente
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] == 'profesor') {
        header("Location: dashboard-profesor.php");
    } else {
        header("Location: dashboard-alumno.php");
    }
    exit();
}

// RUTA CORREGIDA: Conexión centralizada a la base de datos
include '../PHP/conexion.php'; 

$error = ""; 
$email_recuerdo = ""; 

// --- PROCESAMIENTO DEL FORMULARIO ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $conn->real_escape_string($_POST['email']);
    $email_recuerdo = $email; 
    $password = $_POST['password'];

    // Consulta para obtener datos del usuario
    $sql = "SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc(); 
        
        // Verificación de seguridad con HASH
        if (password_verify($password, $usuario['password_hash'])) {
            
            // Seguridad: regeneramos ID para prevenir secuestro de sesión
            session_regenerate_id(true);
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirección inteligente por roles
            if ($usuario['rol'] == 'profesor') {
                header("Location: dashboard-profesor.php");
            } else {
                header("Location: dashboard-alumno.php");
            }
            exit(); 
        } else {
            $error = "La contraseña es incorrecta.";
        }
    } else {
        $error = "No existe una cuenta con este correo electrónico.";
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acceso - ISIMatch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />

    <style>
      .caja-login { 
        border: none; 
        padding: 40px; 
        border-radius: 20px; 
        background-color: white; 
      }
      .input-group-text { cursor: pointer; border: none; }
      .form-control { border: none; padding: 12px; }
      .bg-soft-primary { background-color: #f0f7f8; }
    </style>
  </head>

  <body class="d-flex align-items-center min-vh-100 py-5 bg-light">
    <a href="../index.php" class="position-absolute top-0 start-0 m-4 text-decoration-none text-muted fw-bold">
      <i class="bi bi-arrow-left"></i> Volver al inicio
    </a>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-7">
          <div class="caja-login shadow-lg">
            
            <div class="text-center mb-4">
              <div class="mb-3">
                <i class="bi bi-mortarboard-fill text-primary-custom" style="font-size: 3.5rem;"></i>
              </div>
              <h2 class="fw-bold">¡Hola de nuevo!</h2>
              <p class="text-muted small">Ingresa tus credenciales para continuar</p>
            </div>

            <?php if($error != ""): ?>
              <div class="alert alert-danger border-0 small text-center shadow-sm mb-4">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $error; ?>
              </div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="POST">
              
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <div class="input-group bg-soft-primary rounded-3">
                  <span class="input-group-text bg-transparent text-primary-custom"><i class="bi bi-envelope"></i></span>
                  <input type="email" name="email" class="form-control bg-transparent" placeholder="nombre@ejemplo.com" value="<?php echo htmlspecialchars($email_recuerdo); ?>" required />
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">CONTRASEÑA</label>
                <div class="input-group bg-soft-primary rounded-3">
                  <span class="input-group-text bg-transparent text-primary-custom"><i class="bi bi-lock"></i></span>
                  <input type="password" id="salidaContrasena" name="password" class="form-control bg-transparent" placeholder="••••••••" required />
                  <span class="input-group-text bg-transparent" onclick="togglePassword()">
                    <i class="bi bi-eye text-muted" id="iconoOjo"></i>
                  </span>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="recordar">
                  <label class="form-check-label small text-muted" for="recordar">Recordarme</label>
                </div>
                <a href="#" class="small text-decoration-none fw-bold text-primary-custom">¿Problemas de acceso?</a>
              </div>

              <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow-sm">
                ACCEDER AHORA
              </button>
            </form>

            <div class="text-center mt-4 pt-2">
                <p class="text-muted small">¿Aún no eres miembro? <a href="registro.php" class="text-primary-custom fw-bold text-decoration-none">Crea una cuenta gratis</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      function togglePassword() {
        const input = document.getElementById("salidaContrasena");
        const icono = document.getElementById("iconoOjo");
        if (input.type === "password") {
          input.type = "text";
          icono.classList.replace("bi-eye", "bi-eye-slash");
        } else {
          input.type = "password";
          icono.classList.replace("bi-eye-slash", "bi-eye");
        }
      }

      document.getElementById("loginForm").addEventListener("submit", function (evento) {
        const boton = this.querySelector('button[type="submit"]');
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> VERIFICANDO...';
        boton.classList.add("disabled");
      });
    </script>
  </body>
</html>