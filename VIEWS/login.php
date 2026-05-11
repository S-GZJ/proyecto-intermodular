<?php
/**
 * ISIMatch - Sistema de Acceso (Login)
 * Este archivo gestiona la autenticación de usuarios y la creación de sesiones seguras
 */

//--GESTIÓN DE SESIÓN Y CONEXIÓN--

//Iniciamos la sesión para poder guardar los datos del usuario logueado
session_start(); 

//REDIRECCIÓN AUTOMÁTICA: Si el usuario ya tiene una sesión iniciada, 
//no le dejamos ver el login y lo mandamos directo a su panel.
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol'] == 'profesor') {
        header("Location: dashboard-profesor.php");
    } else {
        header("Location: dashboard-alumno.php");
    }
    exit(); //Detenemos el script para que no cargue el resto de la página
}

// Importamos la conexión centralizada a la base de datos
include '../PHP/conexion.php'; 

$error = ""; //Variable para guardar mensajes de error (ej contraseña mal escrita)
$email_recuerdo = ""; //Para no obligar al usuario a escribir el email de nuevo si falla

//--PROCESAMIENTO DEL FORMULARIO (Cuando el usuario pulsa "Acceder")--
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Limpiamos el email contra inyecciones SQL
    $email = $conn->real_escape_string($_POST['email']);
    $email_recuerdo = $email; 
    $password = $_POST['password'];

    //Buscamos al usuario por su correo electrónico
    $sql = "SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    //Verificamos si existe el usuario
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc(); 
        
        //SEGURIDAD: Comparamos la contraseña escrita con el HASH guardado en la DB
        if (password_verify($password, $usuario['password_hash'])) {
            
            //SEGURIDAD EXTRA: Regeneramos el ID de sesión para evitar ataques de fijación de sesión
            session_regenerate_id(true);
            
            //Guardamos los datos clave en la variable global $_SESSION
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            //Redireccionamos según el rol guardado en la base de datos
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
      /*FUNCIÓN: alternarVisibilidadContrasena
       Esta función permite al usuario ver lo que ha escrito en el campo de contraseña
      o volver a ocultarlo 
       */
      function alternarVisibilidadContrasena() {
        //Buscamos el campo de entrada (input) por su ID 
        const campoEntrada = document.getElementById("entradaContrasena");
        
        //Buscamos el elemento del icono (el ojo) por su ID 
        const iconoOjo = document.getElementById("iconoOjo");

        //Comprobamos el tipo de entrada actual del campo
        if (campoEntrada.type === "password") {
          //Si está oculto (tipo password), lo cambiamos a "text" para que sea visible
          campoEntrada.type = "text";
          
          //Cambiamos visualmente el icono, reemplazamos el ojo normal por el ojo tachado
          iconoOjo.classList.replace("bi-eye", "bi-eye-slash");
        } 
        else {
          //Si ya es visible, lo volvemos a ocultar cambiando el tipo de nuevo a password
          campoEntrada.type = "password";
          
          //Restauramos el icono original (ojo abierto)
          iconoOjo.classList.replace("bi-eye-slash", "bi-eye");
        }
      }

      /*EVENTO: Envío del Formulario (Submit)
       Este bloque se activa automáticamente cuando el usuario pulsa el botón de "ACCEDER"
       */
      document.getElementById("formularioLogin").addEventListener("submit", function (evento) {
        
        //Identificamos el botón de envío dentro del formulario para poder modificarlo
        const botonEnvio = this.querySelector('button[type="submit"]');
        
        //FEEDBACK VISUAL:
        //Modificamos el interior del botón para incluir el círculo de carga 
        //y cambiamos el texto a "VERIFICANDO..." para informar al usuario
        botonEnvio.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> VERIFICANDO...';
        
        //SEGURIDAD Y PREVENCIÓN:
        //Desactivamos el botón (propiedad disabled). Esto evita que un usuario 
        //haga clic varias veces seguidas y envíe peticiones duplicadas al servidor
        botonEnvio.classList.add("disabled");
      });
    </script>
  </body>
</html>