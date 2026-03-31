<?php
session_start(); 

// --- MOTOR PHP: PROCESAMIENTO DEL REGISTRO ---
$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $servidor = "localhost";
    $usuario_db = "root";
    $password_db = "";
    $base_datos = "isimatch";

    $conn = new mysqli($servidor, $usuario_db, $password_db, $base_datos);

    if (!$conn->connect_error) {
        
        $nombre_completo = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $conn->real_escape_string($_POST['rol']);
        $anio_nacimiento = (int)$_POST['anio_nacimiento'];
        
        $partes_nombre = explode(" ", $nombre_completo, 2);
        $nombre = $partes_nombre[0];
        $apellidos = isset($partes_nombre[1]) ? $partes_nombre[1] : "";

        $check_sql = "SELECT id FROM usuarios WHERE email='$email'";
        $resultado = $conn->query($check_sql);
        
        if ($resultado->num_rows > 0) {
            $error = "Ese correo electrónico ya está registrado. Intenta iniciar sesión.";
        } else {
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol, anio_nacimiento) 
                    VALUES ('$nombre', '$apellidos', '$email', '$password', '$rol', $anio_nacimiento)";
            
            if ($conn->query($sql) === TRUE) {
                
                $_SESSION['usuario_id'] = $conn->insert_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;

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
        $conn->close();
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

  <body class="d-flex align-items-center min-vh-100 py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="card card-custom p-5">
            <div class="text-center mb-4">
              <h2 class="fw-bold">Únete a ISIMatch</h2>
              <p class="text-muted">Selecciona tu perfil para empezar</p>
            </div>

            <?php if($error != ""): ?>
              <div class="alert alert-danger small text-center" role="alert">
                <?php echo $error; ?>
              </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
              <div class="col-6">
                <div
                  class="card role-card h-100 p-3 text-center"
                  id="card-alumno"
                  onclick="selectRole('alumno')"
                >
                  <i class="bi bi-emoji-smile role-icon mb-2"></i>
                  <h6 class="fw-bold">Soy Alumno</h6>
                  <small class="text-muted">Quiero aprender</small>
                </div>
              </div>

              <div class="col-6">
                <div
                  class="card role-card h-100 p-3 text-center"
                  id="card-profesor"
                  onclick="selectRole('profesor')"
                >
                  <i class="bi bi-person-workspace role-icon mb-2"></i>
                  <h6 class="fw-bold">Soy Profesor</h6>
                  <small class="text-muted">Quiero enseñar</small>
                </div>
              </div>
            </div>

            <form id="registroForm" action="registro.php" method="POST">
              
              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">NOMBRE COMPLETO</label>
                <input
                  type="text"
                  name="nombre"
                  id="salidaNombre"
                  class="form-control"
                  placeholder="Ej. Juan Pérez"
                />
                <div id="errorNombre" class="text-danger small mt-1" style="display: none;">
                  El nombre debe tener al menos 3 caracteres.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted">CORREO ELECTRÓNICO</label>
                <input
                  type="text"
                  name="email"
                  id="salidaEmail"
                  class="form-control"
                  placeholder="juan@ejemplo.com"
                />
                <div id="errorEmail" class="text-danger small mt-1" style="display: none;">
                  Por favor, ingresa un correo electrónico válido.
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label small fw-bold text-muted">AÑO NACIMIENTO</label>
                  <input
                    type="number"
                    name="anio_nacimiento"
                    id="salidaAño"
                    class="form-control"
                    placeholder="2000"
                  />
                  <div id="errorAño" class="text-danger small mt-1" style="display: none;">
                    Ingresa un año válido (ej. 1995).
                  </div>
                </div>

                <div class="col-6">
                  <label class="form-label small fw-bold text-muted">CONTRASEÑA</label>
                  <input
                    type="password"
                    name="password"
                    id="salidaContraseña"
                    class="form-control"
                    placeholder="********"
                  />
                  <div id="errorContraseña" class="text-danger small mt-1" style="display: none;">
                    Mínimo 6 caracteres.
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-bold text-muted">INTERÉS PRINCIPAL</label>
                <select class="form-select" name="interes" id="salidaInteres">
                  <option value="" disabled selected>Selecciona una materia...</option>
                  <option value="matematicas">Matemáticas</option>
                  <option value="idiomas">Idiomas</option>
                  <option value="programacion">Programación</option>
                </select>
                <div id="errorInteres" class="text-danger small mt-1" style="display: none;">
                  Selecciona una materia principal.
                </div>
              </div>

              <div class="mb-4 form-check">
                <input
                  type="checkbox"
                  class="form-check-input"
                  id="terminos"
                  name="terminos"
                />
                <label class="form-check-label small text-muted" for="terminos">
                  Acepto los
                  <a href="terminos.html" class="text-primary-custom">términos y condiciones</a>
                </label>
                <div id="errorTerminos" class="text-danger small mt-1" style="display: none;">
                  Debes aceptar los términos para continuar.
                </div>
              </div>

              <input type="hidden" id="rol" name="rol" value="" />

              <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-sm">
                CREAR CUENTA GRATIS
              </button>
            </form>

            <p class="text-center mt-4 small text-muted">
              ¿Ya tienes cuenta?
              <a href="login.php" class="text-primary-custom fw-bold">Inicia sesión</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <script>
      /** Función para seleccionar rol */
      function selectRole(role) {
        document
          .querySelectorAll(".role-card")
          .forEach((el) => el.classList.remove("selected"));
        document.getElementById("card-" + role).classList.add("selected");
        document.getElementById("rol").value = role;
      }

      window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        const rolPreseleccionado = urlParams.get("rol");
        if (rolPreseleccionado === "profesor") selectRole("profesor");
        else if (rolPreseleccionado === "alumno") selectRole("alumno");
      };

      /** * FUNCIÓN Cambia el borde a rojo/verde y muestra/oculta el mensaje */
      function mostrarError(salidaId, errorId, hayError) {
        const salida = document.getElementById(salidaId);
        const mensajeError = document.getElementById(errorId);
        
        if (hayError) {
          salida.style.borderColor = "red"; // Rojo
          mensajeError.style.display = "block";
        } else {
          salida.style.borderColor = "green"; // Verde
          mensajeError.style.display = "none";
        }
      }

      /** VALIDACIÓN AL ENVIAR*/
      document.getElementById("registroForm").addEventListener("submit", function (evento) {
        evento.preventDefault(); 
        let esValido = true;

        // Validar Rol
        const rol = document.getElementById("rol").value;
        if (!rol) {
          alert("Por favor, selecciona si eres Alumno o Profesor en las tarjetas superiores");
          esValido = false;
        }

        // Validar Nombre
        const nombreValor = document.getElementById("salidaNombre").value.trim();
        if (nombreValor.length < 3) {
          mostrarError("salidaNombre", "errorNombre", true);
          esValido = false;
        } else {
          mostrarError("salidaNombre", "errorNombre", false);
        }

        // Validar Email 
        const emailValor = document.getElementById("salidaEmail").value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailValor)) {
          mostrarError("salidaEmail", "errorEmail", true);
          esValido = false;
        } else {
          mostrarError("salidaEmail", "errorEmail", false);
        }

        // Validar Año Nacimiento (Lógico)
        const añoValor = parseInt(document.getElementById("salidaAño").value);
        const añoActual = new Date().getFullYear();
        if (isNaN(añoValor) || añoValor < (añoActual - 100) || añoValor > (añoActual - 10)) {
          mostrarError("salidaAño", "errorAño", true);
          esValido = false;
        } else {
          mostrarError("salidaAño", "errorAño", false);
        }

        // Validar Contraseña (CORREGIDO: passValor no existía, ahora usa contraseñaValor)
        const contraseñaValor = document.getElementById("salidaContraseña").value;
        if (contraseñaValor.length < 6) {
          mostrarError("salidaContraseña", "errorContraseña", true);
          esValido = false;
        } else {
          mostrarError("salidaContraseña", "errorContraseña", false);
        }

        // Validar Select Interés
        const interesValor = document.getElementById("salidaInteres").value;
        if (interesValor === "") {
          mostrarError("salidaInteres", "errorInteres", true);
          esValido = false;
        } else {
          mostrarError("salidaInteres", "errorInteres", false);
        }

        // Validar Checkbox Términos
        const checkTerminos = document.getElementById("terminos");
        const errorTerminos = document.getElementById("errorTerminos");
        if (!checkTerminos.checked) {
          checkTerminos.style.outline = "1px solid red";
          errorTerminos.style.display = "block";
          esValido = false;
        } else {
          checkTerminos.style.outline = "none";
          errorTerminos.style.display = "none";
        }

        // Si no hay errores, se envía al PHP
        if (esValido) {
          const boton = this.querySelector('button[type="submit"]');
          boton.innerText = "CREANDO CUENTA...";
          boton.disabled = true;
          this.submit(); 
        }
      });
    </script>
  </body>
</html>