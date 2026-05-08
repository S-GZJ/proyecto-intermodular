<?php
// --- LÓGICA DE CONTROL DE SESIÓN Y SEGURIDAD ---
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

include '../PHP/conexion.php';
$usuario_id = $_SESSION['usuario_id'];
$mensaje_feedback = "";

/*-- PROCESAMIENTO DE CAMBIOS (POST) --*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CASO 1: Actualización de Perfil (Personal y Profesional)
    if (isset($_POST['nombre'])) {
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $apellidos = $conn->real_escape_string($_POST['apellidos']);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $tarifa = (float)$_POST['tarifa'];
        $bio = $conn->real_escape_string($_POST['bio']);
        $linkedin = $conn->real_escape_string($_POST['linkedin']);

        $conn->begin_transaction();
        try {
            $conn->query("UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono' WHERE id='$usuario_id'");
            
            $sql_pd = "INSERT INTO profesores_detalles (usuario_id, titulo_profesional, tarifa_hora, bio, linkedin_url) 
                       VALUES ('$usuario_id', '$titulo', '$tarifa', '$bio', '$linkedin')
                       ON DUPLICATE KEY UPDATE titulo_profesional='$titulo', tarifa_hora='$tarifa', bio='$bio', linkedin_url='$linkedin'";
            $conn->query($sql_pd);

            $conn->commit();
            $_SESSION['nombre'] = $nombre;
            $mensaje_feedback = "<div class='alert alert-success border-0 shadow-sm'>¡Perfil actualizado con éxito!</div>";
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje_feedback = "<div class='alert alert-danger border-0 shadow-sm'>Error al guardar cambios personales.</div>";
        }
    }

    // CASO 2: Cambio de Contraseña (Seguridad)
    if (isset($_POST['pass_actual'])) {
        $actual = $_POST['pass_actual'];
        $nueva = $_POST['pass_nueva'];
        $repetir = $_POST['pass_repetir'];

        $res = $conn->query("SELECT password_hash FROM usuarios WHERE id = '$usuario_id'");
        $user = $res->fetch_assoc();

        if (password_verify($actual, $user['password_hash'])) {
            if ($nueva === $repetir && strlen($nueva) >= 6) {
                $hash = password_hash($nueva, PASSWORD_DEFAULT);
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

/*-- CARGA DE INFORMACIÓN ACTUALIZADA --*/
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, pd.titulo_profesional, pd.tarifa_hora, pd.bio, pd.linkedin_url 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$usuario_id'";
$resultado = $conn->query($sql);
$datos = $resultado->fetch_assoc();

$nombre_mostrar = htmlspecialchars($datos['nombre']);
$letra_inicial = strtoupper(substr($nombre_mostrar, 0, 1));
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
    <style>
      .nav-pills .nav-link { color: var(--text-dark); border-radius: 12px; padding: 15px; margin-bottom: 8px; transition: all 0.3s ease; }
      .nav-pills .nav-link.active { background-color: var(--primary-color) !important; box-shadow: 0 4px 12px rgba(59, 179, 189, 0.3); color: white !important; }
      .btn-check:checked + .btn-outline-primary { background-color: var(--primary-color); border-color: var(--primary-color); color: white; }
    </style>
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
                            <?php $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
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