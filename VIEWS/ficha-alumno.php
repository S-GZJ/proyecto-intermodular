<?php
// --- LÓGICA DE CONTROL DE SESIÓN Y SEGURIDAD ---
session_start();

/*-- ESCUDO DE SEGURIDAD --*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: login.php");
    exit();
}

include '../PHP/conexion.php';
$usuario_id = $_SESSION['usuario_id'];

/*-- PROCESAMIENTO DE CAMBIOS (UPDATE) --*/
$mensaje_feedback = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellidos = $conn->real_escape_string($_POST['apellidos']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $objetivos = $conn->real_escape_string($_POST['objetivos']);
    $nivel = $conn->real_escape_string($_POST['nivel']); // NUEVO CAMPO

    $conn->begin_transaction();

    try {
        // 1. Actualizar tabla principal
        $sql_u = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono' WHERE id='$usuario_id'";
        $conn->query($sql_u);

        // 2. Actualizar detalles del alumno (Incluyendo el Nivel Actual)
        $sql_ad = "INSERT INTO alumnos_detalles (usuario_id, objetivos_aprendizaje, nivel_actual) 
                   VALUES ('$usuario_id', '$objetivos', '$nivel')
                   ON DUPLICATE KEY UPDATE objetivos_aprendizaje='$objetivos', nivel_actual='$nivel'";
        $conn->query($sql_ad);

        $conn->commit();
        $_SESSION['nombre'] = $nombre; 

        $mensaje_feedback = "<div class='alert alert-success alert-dismissible fade show border-0 shadow-sm' role='alert'>
                                <i class='bi bi-check-circle-fill me-2'></i> Perfil actualizado correctamente.
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                             </div>";
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje_feedback = "<div class='alert alert-danger border-0 shadow-sm'>Error al guardar: " . $conn->error . "</div>";
    }
}

/*-- CARGA DE DATOS ACTUALES --*/
$sql = "SELECT u.nombre, u.apellidos, u.email, u.telefono, ad.objetivos_aprendizaje, ad.nivel_actual 
        FROM usuarios u 
        LEFT JOIN alumnos_detalles ad ON u.id = ad.usuario_id 
        WHERE u.id = '$usuario_id'";

$resultado = $conn->query($sql);
$datos = $resultado->fetch_assoc();

$nombre_mostrar = htmlspecialchars($datos['nombre']);
$inicial = strtoupper(substr($nombre_mostrar, 0, 1));
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mi Perfil - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold text-primary-custom" href="dashboard-alumno.php">
            <i class="bi bi-mortarboard-fill"></i> ISIMatch
        </a>
        <div class="ms-auto">
            <a href="dashboard-alumno.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Panel de Alumno
            </a>
        </div>
      </div>
    </nav>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php echo $mensaje_feedback; ?>

                <div class="card border-0 shadow-sm p-4 rounded-4 mb-4 bg-white overflow-hidden position-relative">
                    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 10px;"></div>
                    <div class="d-flex align-items-center gap-4 mt-2">
                        <div class="position-relative">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:90px; height:90px; font-size: 2.5rem;">
                                <?php echo $inicial; ?>
                            </div>
                            <button class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle p-1" title="Cambiar foto">
                                <i class="bi bi-camera-fill" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?php echo $nombre_mostrar . " " . htmlspecialchars($datos['apellidos']); ?></h3>
                            <p class="text-muted mb-1"><?php echo htmlspecialchars($datos['email']); ?></p>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">Perfil Alumno</span>
                        </div>
                    </div>
                </div>

                <form action="ficha-alumno.php" method="POST" class="row g-4">
                    
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-person-circle me-2 text-primary"></i>Datos de Contacto</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">NOMBRE</label>
                                    <input type="text" name="nombre" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">APELLIDOS</label>
                                    <input type="text" name="apellidos" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($datos['apellidos']); ?>" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted">TELÉFONO MÓVIL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted">+34</span>
                                        <input type="tel" name="telefono" class="form-control bg-light border-0 py-2" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>" placeholder="600 000 000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Preferencias Académicas</h5>
                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label small fw-bold text-muted">NIVEL ACADÉMICO ACTUAL</label>
                                    <select name="nivel" class="form-select bg-light border-0 py-2">
                                        <option value="Primaria" <?php echo ($datos['nivel_actual'] == 'Primaria') ? 'selected' : ''; ?>>Educación Primaria</option>
                                        <option value="ESO" <?php echo ($datos['nivel_actual'] == 'ESO') ? 'selected' : ''; ?>>E.S.O.</option>
                                        <option value="Bachillerato" <?php echo ($datos['nivel_actual'] == 'Bachillerato') ? 'selected' : ''; ?>>Bachillerato</option>
                                        <option value="FP" <?php echo ($datos['nivel_actual'] == 'FP') ? 'selected' : ''; ?>>Formación Profesional</option>
                                        <option value="Universidad" <?php echo ($datos['nivel_actual'] == 'Universidad') ? 'selected' : ''; ?>>Grado Universitario / Máster</option>
                                        <option value="Otros" <?php echo ($datos['nivel_actual'] == 'Otros') ? 'selected' : ''; ?>>Otros Estudios / Idiomas</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">MIS OBJETIVOS Y MATERIAS DE INTERÉS</label>
                                    <textarea name="objetivos" class="form-control bg-light border-0" rows="5" placeholder="Ej: Necesito refuerzo en matemáticas de 2º de Bachillerato y me gustaría mejorar mi nivel de conversación en inglés..."><?php echo htmlspecialchars($datos['objetivos_aprendizaje'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-center pb-5">
                        <button type="submit" class="btn btn-primary-custom px-5 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> ACTUALIZAR MI PERFIL
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>