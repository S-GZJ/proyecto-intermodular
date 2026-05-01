<?php
session_start(); // Iniciamos la sesión para guardar los datos del usuario
include 'conexion.php'; // Conectamos a la BD

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Buscamos al usuario en la base de datos
    $sql = "SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Comparamos la contraseña escrita con la encriptada en la BD
        if (password_verify($password, $usuario['password_hash'])) {
            
            // ¡Login correcto! Guardamos sus datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigimos según el rol
            if ($usuario['rol'] == 'profesor') {
                header("Location: dashboard-profesor.php");
            } else {
                header("Location: dashboard-alumno.php");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No existe ninguna cuenta con este correo.";
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
      .caja-login { border: 1px solid #ccc; padding: 30px; border-radius: 10px; background-color: white; }
      .mi-boton { background-color: #0d6efd; color: white; border: none; border-radius: 5px; }
    </style>
  </head>

  <body class="d-flex align-items-center min-vh-100 py-5">
    <a href="index.php" class="position-absolute top-0 start-0 m-4 text-decoration-none text-muted">
      <i class="bi bi-arrow-left"></i> Volver al inicio
    </a>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
          <div class="caja-login shadow-sm">
            <div class="text-center mb-4">
              <h2 style="font-weight: bold">¡Hola de nuevo!</h2>
              <p>Ingresa a tu cuenta para continuar aprendiendo</p>
            </div>

            <?php if($error != ""): ?>
              <div class="alert alert-danger small text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="POST">
              
              <div class="mb-3">
                <label class="form-label"><b>CORREO ELECTRÓNICO</b></label>
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                  <input type="text" id="salidaEmail" name="email" class="form-control" placeholder="nombre@ejemplo.com" />
                </div>
                <div id="errorEmail" class="text-danger small mt-1" style="display: none;">
                  Por favor, ingresa un correo electrónico válido.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label"><b>CONTRASEÑA</b></label>
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                  <input type="password" id="salidaContrasena" name="password" class="form-control" placeholder="********" />
                </div>
                <div id="errorContrasena" class="text-danger small mt-1" style="display: none;">
                  La contraseña no puede estar vacía.
                </div>
              </div>

              <div class="text-end mb-4">
                <a href="#" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
              </div>

              <button type="submit" class="mi-boton w-100 py-3 text-uppercase fw-bold">
                Entrar
              </button>
            </form>

            <hr class="my-4" />

            <p class="text-center small mb-0">
              ¿Aún no tienes cuenta? <br />
              <a href="registro.php" class="fw-bold text-decoration-none">Regístrate gratis</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <script>
      /* Función para mostrar errores y cambiar bordes */
      function mostrarError(salidaId, errorId, hayError) {
        const input = document.getElementById(salidaId);
        const errorMsg = document.getElementById(errorId);
        
        if (hayError) {
          input.style.borderColor = "red"; 
          errorMsg.style.display = "block";
        } else {
          input.style.borderColor = "green";
          errorMsg.style.display = "none";
        }
      }

      document.getElementById("loginForm").addEventListener("submit", function (evento) {
        
        evento.preventDefault(); // Detenemos el envío para validar
        let esValido = true;

        // CORREGIDO: Buscar por "salidaEmail" que es el nombre correcto del ID
        const emailValor = document.getElementById("salidaEmail").value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(emailValor)) {
          mostrarError("salidaEmail", "errorEmail", true);
          esValido = false;
        } else {
          mostrarError("salidaEmail", "errorEmail", false);
        }

        
        const passValor = document.getElementById("salidaContrasena").value;
        if (passValor.trim() === "") {
          mostrarError("salidaContrasena", "errorContrasena", true);
          esValido = false;
        } else {
          mostrarError("salidaContrasena", "errorContrasena", false);
        }

        //Si todo es correcto, envía los datos a PHP
        if (esValido) {
          const boton = this.querySelector('button[type="submit"]');
          boton.innerText = "VERIFICANDO...";
          boton.disabled = true;
          this.submit(); // Llama al envío real
        }
      });
    </script>
  </body>
</html>