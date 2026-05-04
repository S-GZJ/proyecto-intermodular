<?php
/**
 * SISTEMA DE REGISTRO DE USUARIOS (registro.php)
 * Este script procesa el alta de nuevos usuarios y gestiona la sesión inicial.
 */
session_start(); 

$error = ""; 

// --- MOTOR PHP: PROCESAMIENTO DEL FORMULARIO ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Configuración de acceso a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $password_db = "";
    $base_datos = "isimatch";

    // 1. Conexión al servidor MySQL
    $conn = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

    if (!$conn->connect_error) {
        
        // 2. Saneamiento de entradas para evitar Inyección SQL
        $nombre_completo = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        
        // Encriptación de contraseña mediante el algoritmo BCRYPT
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $rol = $conn->real_escape_string($_POST['rol']);
        $anio_nacimiento = (int)$_POST['anio_nacimiento'];
        
        // Lógica para separar el nombre de los apellidos (divide por el primer espacio encontrado)
        $partes_nombre = explode(" ", $nombre_completo, 2);
        $nombre = $partes_nombre[0];
        $apellidos = isset($partes_nombre[1]) ? $partes_nombre[1] : "";

        // 3. Verificación de duplicados: Comprobamos si el email ya existe
        $check_sql = "SELECT id FROM usuarios WHERE email='$email'";
        $resultado = $conn->query($check_sql);
        
        if ($resultado->num_rows > 0) {
            $error = "Ese correo electrónico ya está registrado. Intenta iniciar sesión.";
        } else {
            // 4. Inserción del nuevo usuario en la tabla 'usuarios'
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol, anio_nacimiento) 
                    VALUES ('$nombre', '$apellidos', '$email', '$password', '$rol', $anio_nacimiento)";
            
            if ($conn->query($sql) === TRUE) {
                
                // Registro exitoso: Iniciamos la sesión automáticamente con el ID recién creado
                $_SESSION['usuario_id'] = $conn->insert_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;

                // Redirección inteligente según el rol elegido
                if ($rol === "profesor") {
                    header("Location: dashboard-profesor.php");
                } else {
                    header("Location: dashboard-alumno.php");
                }
                exit(); 
            } else {
                $error = "Hubo un error al crear la cuenta: " . $conn->error;
            }
        }
        $conn->close(); // Cerramos conexión por seguridad
    } else {
        $error = "Error de conexión a la base de datos.";
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Crea tu cuenta - ISIMatch</title>
    <!-- Bootstrap para diseño y estilos personalizados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
  </head>

  <body class="d-flex align-items-center min-vh-100 py-5 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="card card-custom p-5 shadow-sm border-0">
            <div class="text-center mb-4">
              <h2 class="fw-bold">Únete a ISIMatch</h2>
              <p class="text-muted">Selecciona tu perfil para empezar</p>
            </div>

            <!-- Bloque de errores PHP -->
            <?php if($error != ""): ?>
              <div class="alert alert-danger small text-center" role="alert">
                <?php echo $error; ?>
              </div>
            <?php endif; ?>

            <!-- SELECTOR DE ROL: Interfaz visual para elegir perfil -->
            <div class="row g-3 mb-4">
              <div class="col-6">
                <div class="card role-card h-100 p-3 text-center" id="card-alumno" onclick="selectRole('alumno')">
                  <i class="bi bi-emoji-smile role-icon mb-2"></i>
                  <h6 class="fw-bold">Soy Alumno</h6>
                  <small class="text-muted">Quiero aprender</small>
                </div>
              </div>

              <div class="col-6">
                <div class="card role-card h-100 p-3 text-center" id="card-profesor" onclick="selectRole('profesor')">
                  <i class="bi bi-person-workspace role-icon mb-2"></i>
                  <h6 class="fw-bold">Soy Profesor</h6>
                  <small class="text-muted">Quiero enseñar</small>
                </div>
              </div>
            </div>

            <!-- FORMULARIO DE REGISTRO -->
            <form id="registroForm" action="registro.php" method="POST">
              
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">NOMBRE COMPLETO</label>
                <input type="text" name="nombre" id="salidaNombre" class="form-control" placeholder="Ej. Juan Pérez" />
                <div id="errorNombre" class="text-danger small mt-1" style="display: none;">Mínimo 3 caracteres.</div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <input type="text" name="email" id="salidaEmail" class="form-control" placeholder="juan@ejemplo.com" />
                <div id="errorEmail" class="text-danger small mt-1" style="display: none;">Ingresa un correo válido.</div>
              </div>

              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label small fw-bold text-muted">AÑO NACIMIENTO</label>
                  <input type="number" name="anio_nacimiento" id="salidaAño" class="form-control" placeholder="2000" />
                  <div id="errorAño" class="text-danger small mt-1" style="display: none;">Año no válido.</div>
                </div>

                <div class="col-6">
                  <label class="form-label small fw-bold text-muted">CONTRASEÑA</label>
                  <input type="password" name="password" id="salidaContraseña" class="form-control" placeholder="********" />
                  <div id="errorContraseña" class="text-danger small mt-1" style="display: none;">Mínimo 6 caracteres.</div>
                </div>
              </div>

              <!-- Campo oculto que almacena el rol seleccionado mediante JS -->
              <input type="hidden" id="rol" name="rol" value="" />

              <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-sm fw-bold">
                CREAR CUENTA GRATIS
              </button>
            </form>

            <p class="text-center mt-4 small text-muted">
              ¿Ya tienes cuenta? <a href="login.php" class="text-primary-custom fw-bold">Inicia sesión</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <script>
      /**
       * LÓGICA DE SELECCIÓN DE ROL
       * Cambia visualmente las tarjetas y actualiza el valor del input hidden 'rol'.
       */
      function selectRole(role) {
        document.querySelectorAll(".role-card").forEach((el) => el.classList.remove("selected"));
        document.getElementById("card-" + role).classList.add("selected");
        document.getElementById("rol").value = role;
      }

      // Preselección de rol mediante parámetros de URL (si existen)
      window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        const rolPreseleccionado = urlParams.get("rol");
        if (rolPreseleccionado === "profesor") selectRole("profesor");
        else if (rolPreseleccionado === "alumno") selectRole("alumno");
      };

      /** FUNCIÓN AUXILIAR DE VALIDACIÓN VISUAL */
      function mostrarError(salidaId, errorId, hayError) {
        const salida = document.getElementById(salidaId);
        const mensajeError = document.getElementById(errorId);
        if (hayError) {
          salida.style.borderColor = "red";
          mensajeError.style.display = "block";
        } else {
          salida.style.borderColor = "green";
          mensajeError.style.display = "none";
        }
      }

      /** VALIDACIÓN DEL FORMULARIO ANTES DEL ENVÍO */
      document.getElementById("registroForm").addEventListener("submit", function (evento) {
        evento.preventDefault(); // Pausamos el envío para verificar
        let esValido = true;

        // 1. Validar si seleccionó Rol
        const rol = document.getElementById("rol").value;
        if (!rol) {
          alert("Por favor, selecciona si eres Alumno o Profesor");
          esValido = false;
        }

        // 2. Validar Nombre
        const nombreValor = document.getElementById("salidaNombre").value.trim();
        if (nombreValor.length < 3) {
          mostrarError("salidaNombre", "errorNombre", true);
          esValido = false;
        } else { mostrarError("salidaNombre", "errorNombre", false); }

        // 3. Validar Email mediante Expresión Regular
        const emailValor = document.getElementById("salidaEmail").value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailValor)) {
          mostrarError("salidaEmail", "errorEmail", true);
          esValido = false;
        } else { mostrarError("salidaEmail", "errorEmail", false); }

        // 4. Validar Año (debe ser coherente, ni mayor al actual ni menor a 100 años)
        const añoValor = parseInt(document.getElementById("salidaAño").value);
        const añoActual = new Date().getFullYear();
        if (isNaN(añoValor) || añoValor < (añoActual - 100) || añoValor > (añoActual - 10)) {
          mostrarError("salidaAño", "errorAño", true);
          esValido = false;
        } else { mostrarError("salidaAño", "errorAño", false); }

        // 5. Validar Contraseña
        const contraseñaValor = document.getElementById("salidaContraseña").value;
        if (contraseñaValor.length < 6) {
          mostrarError("salidaContraseña", "errorContraseña", true);
          esValido = false;
        } else { mostrarError("salidaContraseña", "errorContraseña", false); }

        // Envío final si todo es correcto
        if (esValido) {
          const boton = this.querySelector('button[type="submit"]');
          boton.innerText = "CREANDO CUENTA...";
          boton.disabled = true;
          this.submit(); // Llama al envío real hacia el motor PHP
        }
      });
    </script>
  </body>
</html>