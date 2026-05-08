<?php
/*--SISTEMA DE REGISTRO DE USUARIOS (registro.php)--*/
session_start(); 

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    include '../PHP/conexion.php';

    if (!$conn->connect_error) {
        
        // Saneamiento de entradas
        $nombre_completo = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $conn->real_escape_string($_POST['rol']);
        $anio_nacimiento = (int)$_POST['anio_nacimiento'];
        
        // Separación de nombre y apellidos
        $partes_nombre = explode(" ", $nombre_completo, 2);
        $nombre = $partes_nombre[0];
        $apellidos = isset($partes_nombre[1]) ? $partes_nombre[1] : "";

        // Verificación de duplicados
        $check_sql = "SELECT id FROM usuarios WHERE email='$email'";
        $resultado = $conn->query($check_sql);
        
        if ($resultado->num_rows > 0) {
            $error = "Este correo ya está en uso. ¿Ya tienes cuenta?";
        } else {
            // 1. INSERCIÓN DEL USUARIO
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol, anio_nacimiento) 
                    VALUES ('$nombre', '$apellidos', '$email', '$password', '$rol', $anio_nacimiento)";
            
            if ($conn->query($sql) === TRUE) {
                $nuevo_id = $conn->insert_id; // Guardamos el ID recién creado

                // --- INICIO LÓGICA MENSAJE DE BIENVENIDA ---
                // El remitente 1 debe existir en tu BDD como 'Sistema' o 'Admin'
                $id_admin = 1; 
                $texto_bienvenida = "¡Hola " . $nombre . "! Bienvenido a ISIMatch. Estamos encantados de tenerte aquí. Explora la plataforma y cuéntanos si necesitas ayuda.";
                
                $sql_mensaje = "INSERT INTO mensajes (remitente_id, destinatario_id, contenido, leido) 
                                VALUES ('$id_admin', '$nuevo_id', '$texto_bienvenida', 0)";
                $conn->query($sql_mensaje);
                // --- FIN LÓGICA MENSAJE ---

                // Login automático
                $_SESSION['usuario_id'] = $nuevo_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;

                // Redirección inteligente
                if ($rol === "profesor") {
                    header("Location: dashboard-profesor.php");
                } else {
                    header("Location: dashboard-alumno.php");
                }
                exit(); 
            } else {
                $error = "Error crítico al crear la cuenta: " . $conn->error;
            }
        }
        $conn->close(); 
    } else {
        $error = "Error de conexión con el servidor.";
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crea tu cuenta - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />

    <style>
      .role-card { cursor: pointer; transition: transform 0.2s, border-color 0.3s; border: 2px solid #eee; }
      .role-card:hover { transform: translateY(-5px); border-color: var(--primary-color); }
      .role-card.selected { border-color: var(--primary-color); background-color: rgba(59, 179, 189, 0.05); }
      .password-strength { height: 5px; transition: all 0.3s; border-radius: 5px; margin-top: 5px; width: 0%; }
      .bg-soft { background-color: #f8f9fa; }
    </style>
  </head>

  <body class="d-flex align-items-center min-vh-100 py-5 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
          <div class="card card-custom p-5 shadow-lg border-0 rounded-4 bg-white">
            
            <div class="text-center mb-5">
              <i class="bi bi-mortarboard-fill text-primary-custom fs-1 mb-2"></i>
              <h2 class="fw-bold">Únete a ISIMatch</h2>
              <p class="text-muted">Tu nueva forma de aprender y enseñar online</p>
            </div>

            <?php if($error != ""): ?>
              <div class="alert alert-danger border-0 small text-center shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
              </div>
            <?php endif; ?>

            <form id="registroForm" action="registro.php" method="POST">
              
              <label class="form-label small fw-bold text-muted text-uppercase mb-3">Selecciona tu perfil</label>
              <div class="row g-3 mb-4">
                <div class="col-6">
                  <div class="card role-card h-100 p-3 text-center rounded-4" id="card-alumno" onclick="selectRole('alumno')">
                    <i class="bi bi-person-badge fs-2 mb-2 text-primary-custom"></i>
                    <h6 class="fw-bold mb-0">Alumno</h6>
                  </div>
                </div>
                <div class="col-6">
                  <div class="card role-card h-100 p-3 text-center rounded-4" id="card-profesor" onclick="selectRole('profesor')">
                    <i class="bi bi-person-workspace fs-2 mb-2 text-primary-custom"></i>
                    <h6 class="fw-bold mb-0">Profesor</h6>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Nombre y Apellidos</label>
                <input type="text" name="nombre" class="form-control rounded-pill border-0 bg-soft px-4 py-2" placeholder="Nombre completo" required />
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                <input type="email" name="email" class="form-control rounded-pill border-0 bg-soft px-4 py-2" placeholder="email@ejemplo.com" required />
              </div>

              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label small fw-bold text-muted text-uppercase">Año Nacimiento</label>
                  <input type="number" name="anio_nacimiento" class="form-control rounded-pill border-0 bg-soft px-4 py-2" placeholder="Ej. 1995" required />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-bold text-muted text-uppercase">Contraseña</label>
                  <input type="password" name="password" class="form-control rounded-pill border-0 bg-soft px-4 py-2" placeholder="••••••••" onkeyup="checkStrength(this.value)" required />
                  <div class="password-strength" id="strengthBar"></div>
                </div>
              </div>

              <input type="hidden" id="rol" name="rol" value="" />

              <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="termsCheck" required>
                <label class="form-check-label small text-muted" for="termsCheck">Acepto los términos y condiciones.</label>
              </div>

              <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-sm fw-bold rounded-pill">
                REGISTRARME AHORA
              </button>
            </form>

            <p class="text-center mt-4 small text-muted">
              ¿Ya eres parte de la comunidad? <a href="login.php" class="text-primary-custom fw-bold text-decoration-none">Inicia sesión</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <script>
      function selectRole(role) {
        document.querySelectorAll(".role-card").forEach((el) => el.classList.remove("selected"));
        document.getElementById("card-" + role).classList.add("selected");
        document.getElementById("rol").value = role;
      }

      function checkStrength(password) {
        const bar = document.getElementById("strengthBar");
        let strength = 0;
        if (password.length > 5) strength += 33;
        if (/[A-Z]/.test(password)) strength += 33;
        if (/[0-9]/.test(password)) strength += 34;

        bar.style.width = strength + "%";
        if (strength < 34) bar.style.backgroundColor = "#ff4d4d";
        else if (strength < 67) bar.style.backgroundColor = "#ffd11a";
        else bar.style.backgroundColor = "#2eb82e";
      }

      document.getElementById("registroForm").addEventListener("submit", function (evento) {
        if (!document.getElementById("rol").value) {
          evento.preventDefault();
          alert("Por favor, selecciona si eres Alumno o Profesor antes de continuar.");
          return;
        }
        const boton = this.querySelector('button[type="submit"]');
        boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...';
        boton.classList.add("disabled");
      });
    </script>
  </body>
</html>