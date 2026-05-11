<?php
/**
 * ISIMatch - Configuración del Profesor
 * Gestiona el perfil personal, profesional, disponibilidad y seguridad
 */

//--LÓGICA DE CONTROL DE SESIÓN Y SEGURIDAD--
session_start();

//Escudo de seguridad: Solo permite el acceso si el usuario está logueado Y es profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

include '../PHP/conexion.php';
$usuario_id = $_SESSION['usuario_id'];
$mensaje_feedback = ""; // Variable para mostrar alertas de éxito o error

/*--PROCESAMIENTO DE CAMBIOS (Cuando el usuario pulsa un botón de guardar)--*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //CASO 1: Actualización de Perfil (Datos Personales + Perfil Público)
    if (isset($_POST['nombre'])) {
        // Limpiamos los datos para evitar inyecciones SQL
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $apellidos = $conn->real_escape_string($_POST['apellidos']);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $tarifa = (float)$_POST['tarifa'];
        $bio = $conn->real_escape_string($_POST['bio']);
        $linkedin = $conn->real_escape_string($_POST['linkedin']);

        //Usamos transacciones, es decir, o se guardan todos los datos o ninguno (evita datos corruptos)
        $conn->begin_transaction();
        try {
            //Actualizamos la tabla principal (usuarios)
            $conn->query("UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono' WHERE id='$usuario_id'");
            
            //Actualizamos o insertamos en la tabla de detalles profesionales
            //ON DUPLICATE KEY UPDATE: si ya existe el ID, actualiza; si no, lo crea
            $sql_pd = "INSERT INTO profesores_detalles (usuario_id, titulo_profesional, tarifa_hora, bio, linkedin_url) 
                       VALUES ('$usuario_id', '$titulo', '$tarifa', '$bio', '$linkedin')
                       ON DUPLICATE KEY UPDATE titulo_profesional='$titulo', tarifa_hora='$tarifa', bio='$bio', linkedin_url='$linkedin'";
            $conn->query($sql_pd);

            $conn->commit(); // Confirmamos los cambios en la base de datos
            $_SESSION['nombre'] = $nombre; // Actualizamos el nombre en la sesión actual
            $mensaje_feedback = "<div class='alert alert-success border-0 shadow-sm'>¡Perfil actualizado con éxito!</div>";
        } catch (Exception $e) {
            $conn->rollback(); // Si algo falla, deshacemos todo lo anterior
            $mensaje_feedback = "<div class='alert alert-danger border-0 shadow-sm'>Error al guardar cambios personales.</div>";
        }
    }

    //CASO 2: Cambio de Contraseña
    if (isset($_POST['pass_actual'])) {
        $actual = $_POST['pass_actual'];
        $nueva = $_POST['pass_nueva'];
        $repetir = $_POST['pass_repetir'];

        //Buscamos la contraseña actual (encriptada) en la base de datos
        $res = $conn->query("SELECT password_hash FROM usuarios WHERE id = '$usuario_id'");
        $user = $res->fetch_assoc();

        //Verificamos si la contraseña que escribió coincide con la guardada
        if (password_verify($actual, $user['password_hash'])) {
            //Comprobamos que la nueva contraseña coincida y sea segura (mínimo 6 caracteres)
            if ($nueva === $repetir && strlen($nueva) >= 6) {
                $hash = password_hash($nueva, PASSWORD_DEFAULT); // Encriptamos la nueva
                $conn->query("UPDATE usuarios SET password_hash = '$hash' WHERE id = '$usuario_id'");
                $mensaje_feedback = "<div class='alert alert-success border-0 shadow-sm'>Contraseña actualizada.</div>";
            } else {
                $mensaje_feedback = "<div class='alert alert-danger border-0 shadow-sm'>Las contraseñas no coinciden o son muy cortas.</div>";
            }
        } else {
            $mensaje_feedback = "<div class='alert alert-danger border-0 shadow-sm'>La contraseña actual es incorrecta.</div>";
        }
    }
}

/*--CARGA DE INFORMACIÓN ACTUALIZADA (Para rellenar los inputs del formulario)--*/
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, pd.titulo_profesional, pd.tarifa_hora, pd.bio, pd.linkedin_url 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$usuario_id'";
$resultado = $conn->query($sql);
$datos = $resultado->fetch_assoc();

$nombre_mostrar = htmlspecialchars($datos['nombre']);
$letra_inicial = strtoupper(substr($nombre_mostrar, 0, 1)); //Inicial para el avatar circular
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configuración - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">
    
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard-profesor.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i> ISIMatch
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
             <a href="dashboard-profesor.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Volver al Panel</a>
             <img src="https://placehold.co/35x35/FFC947/white?text=<?php echo $letra_inicial; ?>" class="rounded-circle border">
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <?php echo $mensaje_feedback; ?>

      <div class="row g-4">
        <div class="col-lg-3">
          <div class="card card-custom p-2 sticky-top border-0 shadow-sm" style="top: 100px;">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
              <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-personal" type="button"><i class="bi bi-person me-2"></i> Datos e Información</button>
              <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-horario" type="button"><i class="bi bi-calendar-event me-2"></i> Disponibilidad</button>
              <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#v-pills-seguridad" type="button"><i class="bi bi-shield-lock me-2"></i> Seguridad</button>
            </div>
          </div>
        </div>

        <div class="col-lg-9">
            <div class="tab-content" id="v-pills-tabContent">
              
              <div class="tab-pane fade show active" id="v-pills-personal" role="tabpanel">
                <form action="configuracion-profesor.php" method="POST">
                    <div class="card card-custom p-4 border-0 shadow-sm rounded-4 mb-4">
                        <h5 class="fw-bold mb-4">Información Básica</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="small fw-bold">NOMBRE</label><input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required></div>
                            <div class="col-md-6"><label class="small fw-bold">APELLIDOS</label><input type="text" name="apellidos" class="form-control" value="<?php echo htmlspecialchars($datos['apellidos']); ?>" required></div>
                            <div class="col-md-6"><label class="small fw-bold">TELÉFONO</label><input type="tel" name="telefono" class="form-control" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>"></div>
                        </div>
                    </div>
                    <div class="card card-custom p-4 border-0 shadow-sm rounded-4 mb-4">
                        <h5 class="fw-bold mb-4">Perfil Público</h5>
                        <div class="row g-3">
                            <div class="col-12"><label class="small fw-bold">TÍTULO PROFESIONAL</label><input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($datos['titulo_profesional'] ?? ''); ?>"></div>
                            <div class="col-md-4"><label class="small fw-bold">TARIFA (€/h)</label><input type="number" step="0.01" name="tarifa" class="form-control" value="<?php echo htmlspecialchars($datos['tarifa_hora'] ?? '15.00'); ?>"></div>
                            <div class="col-12"><label class="small fw-bold">BIOGRAFÍA</label><textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($datos['bio'] ?? ''); ?></textarea></div>
                            <div class="col-md-6"><label class="small fw-bold">LINKEDIN (URL)</label><input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($datos['linkedin_url'] ?? ''); ?>"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold">GUARDAR PERFIL</button>
                </form>
              </div>

              <div class="tab-pane fade" id="v-pills-horario" role="tabpanel">
                <form action="configuracion-profesor.php" method="POST">
                    <div class="card card-custom p-4 border-0 shadow-sm rounded-4">
                        <h5 class="fw-bold mb-4">Horario Semanal</h5>
                        <div class="row g-2">
                            <?php 
                            // Generamos los días de la semana dinámicamente con un bucle
                            $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
                            foreach($dias as $dia): ?>
                            <div class="col-12 d-md-flex align-items-center gap-3 p-3 border-bottom">
                                <div style="width: 100px;" class="fw-bold"><?php echo $dia; ?></div>
                                <div class="btn-group shadow-sm">
                                    <input type="checkbox" class="btn-check" id="<?php echo $dia; ?>-M" name="disp[<?php echo $dia; ?>][]" value="Mañana">
                                    <label class="btn btn-outline-primary btn-sm px-3" for="<?php echo $dia; ?>-M">Mañana</label>
                                    
                                    <input type="checkbox" class="btn-check" id="<?php echo $dia; ?>-T" name="disp[<?php echo $dia; ?>][]" value="Tarde">
                                    <label class="btn btn-outline-primary btn-sm px-3" for="<?php echo $dia; ?>-T">Tarde</label>
                                    
                                    <input type="checkbox" class="btn-check" id="<?php echo $dia; ?>-N" name="disp[<?php echo $dia; ?>][]" value="Noche">
                                    <label class="btn btn-outline-primary btn-sm px-3" for="<?php echo $dia; ?>-N">Noche</label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary-custom mt-4 py-3 rounded-pill fw-bold">ACTUALIZAR DISPONIBILIDAD</button>
                    </div>
                </form>
              </div>

              <div class="tab-pane fade" id="v-pills-seguridad" role="tabpanel">
                <form action="configuracion-profesor.php" method="POST">
                    <div class="card card-custom p-4 border-0 shadow-sm rounded-4">
                        <h5 class="fw-bold mb-4">Cambiar Contraseña</h5>
                        <div class="mb-3"><label class="small fw-bold">CONTRASEÑA ACTUAL</label><input type="password" name="pass_actual" class="form-control" required></div>
                        <div class="mb-3"><label class="small fw-bold">NUEVA CONTRASEÑA</label><input type="password" name="pass_nueva" class="form-control" required></div>
                        <div class="mb-4"><label class="small fw-bold">REPETIR NUEVA CONTRASEÑA</label><input type="password" name="pass_repetir" class="form-control" required></div>
                        <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold">ACTUALIZAR SEGURIDAD</button>
                    </div>
                </form>
              </div>

            </div>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>