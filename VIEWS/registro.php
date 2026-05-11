<?php
/**
 * ISIMatch - Sistema de Registro de Usuarios
 * Gestiona la creación de cuentas, validación de correos duplicados y bienvenida automática
 */

session_start(); 

$error = ""; //Variable para capturar y mostrar errores al usuario

//--LÓGICA DE PROCESAMIENTO (Cuando el usuario envía el formulario) --
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    include '../PHP/conexion.php';

    if (!$conn->connect_error) {
        
        //Saneamiento de entradas, evitamos inyecciones SQL limpiando los textos
        $nombre_completo = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        
        //SEGURIDAD: Encriptamos la contraseña antes de guardarla, nunca se guarda en texto plano
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $rol = $conn->real_escape_string($_POST['rol']);
        $anio_nacimiento = (int)$_POST['anio_nacimiento'];
        
        //Lógica para separar el nombre de los apellidos si el usuario escribe todo junto
        $partes_nombre = explode(" ", $nombre_completo, 2);
        $nombre = $partes_nombre[0];
        $apellidos = isset($partes_nombre[1]) ? $partes_nombre[1] : "";

        //VERIFICACIÓN, comprobamos si el correo ya existe para no duplicar cuentas
        $check_sql = "SELECT id FROM usuarios WHERE email='$email'";
        $resultado = $conn->query($check_sql);
        
        if ($resultado->num_rows > 0) {
            $error = "Este correo ya está en uso. ¿Ya tienes cuenta?";
        } else {
            //--INSERCIÓN DEL NUEVO USUARIO--
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol, anio_nacimiento) 
                    VALUES ('$nombre', '$apellidos', '$email', '$password', '$rol', $anio_nacimiento)";
            
            if ($conn->query($sql) === TRUE) {
                $nuevo_id = $conn->insert_id; //Obtenemos el ID generado automáticamente por MySQL

                //--INICIO LÓGICA MENSAJE DE BIENVENIDA--
                //Creamos un mensaje automático en la tabla de mensajes para el nuevo usuario
                $id_admin = 1; //ID del administrador o sistema
                $texto_bienvenida = "¡Hola " . $nombre . "! Bienvenido a ISIMatch. Estamos encantados de tenerte aquí.";
                
                $sql_mensaje = "INSERT INTO mensajes (remitente_id, destinatario_id, contenido, leido) 
                                VALUES ('$id_admin', '$nuevo_id', '$texto_bienvenida', 0)";
                $conn->query($sql_mensaje);
                //--FIN LÓGICA MENSAJE--

                //LOGIN AUTOMÁTICO, una vez registrado, le iniciamos sesión sin pedirle el login
                $_SESSION['usuario_id'] = $nuevo_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;

                //Redirección según el perfil elegido
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
      /*Estilos para las tarjetas de selección de rol (Alumno/Profesor)*/
      .role-card { cursor: pointer; transition: transform 0.2s, border-color 0.3s; border: 2px solid #eee; }
      .role-card:hover { transform: translateY(-5px); border-color: #3bb3bd; }
      .role-card.selected { border-color: #3bb3bd; background-color: rgba(59, 179, 189, 0.05); }
      /*Barra de fortaleza de contraseña*/
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
                  <div class="card role-card h-100 p-3 text-center rounded-4" id="card-alumno" onclick="seleccionarRol('alumno')">
                    <i class="bi bi-person-badge fs-2 mb-2 text-primary-custom"></i>
                    <h6 class="fw-bold mb-0">Alumno</h6>
                  </div>
                </div>
                <div class="col-6">
                  <div class="card role-card h-100 p-3 text-center rounded-4" id="card-profesor" onclick="seleccionarRol('profesor')">
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
                  <input type="password" name="password" class="form-control rounded-pill border-0 bg-soft px-4 py-2" placeholder="••••••••" onkeyup="verificarFortaleza(this.value)" required />
                  <div class="password-strength" id="barraFortaleza"></div>
                </div>
              </div>

              <input type="hidden" id="rol_oculto" name="rol" value="" />

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
      /*Función para seleccionar el rol visualmente y asignar el valor al input oculto*/
      function seleccionarRol(rol) {
        document.querySelectorAll(".role-card").forEach((el) => el.classList.remove("selected"));
        document.getElementById("card-" + rol).classList.add("selected");
        document.getElementById("rol_oculto").value = rol;
      }

      /*Cálculo visual de la fortaleza de la contraseña (Longitud, mayúsculas y números)*/
      function verificarFortaleza(contrasena) {
        const barra = document.getElementById("barraFortaleza");
        let fuerza = 0;
        if (contrasena.length > 5) fuerza += 33;
        if (/[A-Z]/.test(contrasena)) fuerza += 33;
        if (/[0-9]/.test(contrasena)) fuerza += 34;

        barra.style.width = fuerza + "%";
        if (fuerza < 34) barra.style.backgroundColor = "#ff4d4d"; //Débil
        else if (fuerza < 67) barra.style.backgroundColor = "#ffd11a"; //Media
        else barra.style.backgroundColor = "#2eb82e"; //Fuerte
      }

      /*Validación antes de enviar, comprueba que se haya elegido un rol*/
      document.getElementById("registroForm").addEventListener("submit", function (evento) {
        if (!document.getElementById("rol_oculto").value) {
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