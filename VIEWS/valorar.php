<?php
/**
 * ISIMatch - Formulario de valoración de profesores
 *
 * Permite a un alumno valorar una clase ya realizada mediante una
 * puntuación de 1 a 5 estrellas y un comentario opcional.
 *
 * Reglas de seguridad:
 * - Solo los alumnos autenticados pueden acceder.
 * - La clase debe pertenecer al alumno.
 * - La clase debe estar aceptada y haber finalizado
 *   (fecha_hora < NOW()).
 * - Solo se permite una valoración por clase.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*-- ESCUDO DE SEGURIDAD --*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

include '../PHP/conexion.php';

$alumno_id    = $_SESSION['usuario_id'];
$nombre_alumno = htmlspecialchars($_SESSION['nombre']);

/*-- VALIDAR PARÁMETRO --*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard-alumno.php");
    exit();
}

$clase_id = (int) $_GET['id'];


/*-- OBTENER LA CLASE Y COMPROBAR QUE YA HA SIDO IMPARTIDA --*/
$stmt = $conn->prepare("
    SELECT c.id, c.materia_nombre_manual, c.fecha_hora, c.profesor_id,
           u.nombre AS profe_nombre, u.apellidos AS profe_apellidos
    FROM clases c
    JOIN usuarios u ON c.profesor_id = u.id
    WHERE c.id = ? AND c.alumno_id = ? AND c.estado = 'aceptada' AND c.fecha_hora < NOW()
");
$stmt->bind_param("ii", $clase_id, $alumno_id);
$stmt->execute();
$res_clase = $stmt->get_result();
$clase = $res_clase->fetch_assoc();
$stmt->close();

if (!$clase) {
    // La clase no existe, no pertenece al alumno o todavía no puede ser valorada
    header("Location: dashboard-alumno.php?error=clase_invalida");
    exit();
}

/*-- VERIFICAR SI YA EXISTE RESEÑA PARA ESTA CLASE --*/
$stmt2 = $conn->prepare("SELECT id FROM resenas WHERE clase_id = ? AND alumno_id = ?");
$stmt2->bind_param("ii", $clase_id, $alumno_id);
$stmt2->execute();
$stmt2->store_result();
$ya_valorada = ($stmt2->num_rows > 0);
$stmt2->close();

// Preparar datos de presentación
$profe_nombre_completo = htmlspecialchars($clase['profe_nombre'] . ' ' . $clase['profe_apellidos']);
$materia               = htmlspecialchars($clase['materia_nombre_manual']);
$fecha_clase           = date('d \d\e F \d\e Y', strtotime($clase['fecha_hora']));

// Leer mensajes de redirección (por si volvemos tras un error del controlador)
$mensaje_error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'puntuacion':   $mensaje_error = 'La puntuación debe estar entre 1 y 5.'; break;
        case 'duplicada':    $mensaje_error = 'Ya has valorado esta clase anteriormente.'; break;
        case 'permisos':     $mensaje_error = 'No tienes permiso para valorar esta clase.'; break;
        case 'estado':       $mensaje_error = 'Solo puedes valorar clases cuya fecha ya haya finalizado.';; break;
        default:             $mensaje_error = 'Ha ocurrido un error. Inténtalo de nuevo.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valorar a <?php echo $profe_nombre_completo; ?> - ISIMatch</title>
    <meta name="description" content="Valora tu experiencia con el profesor <?php echo $profe_nombre_completo; ?> en ISIMatch.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../CSS/style.css" />
    <style>
        /* ===== SISTEMA DE ESTRELLAS INTERACTIVO ===== */
        .star-rating-group {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 8px;
        }

        .star-rating-group input[type="radio"] {
            display: none;
        }

        .star-rating-group label {
            font-size: 3rem;
            color: #dee2e6;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.15s ease;
            line-height: 1;
        }

        /* Hover: iluminar la estrella y todas las que están a su derecha */
        .star-rating-group label:hover,
        .star-rating-group label:hover ~ label,
        .star-rating-group input[type="radio"]:checked ~ label {
            color: #ffc947;
            transform: scale(1.15);
        }

        /* Etiqueta de puntuación */
        .rating-label-text {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            min-height: 1.4rem;
            transition: color 0.2s;
        }

        /* Tarjeta de resumen de clase */
        .clase-info-card {
            background: linear-gradient(135deg, rgba(59,179,189,0.08) 0%, rgba(59,179,189,0.02) 100%);
            border-left: 4px solid var(--primary-color) !important;
        }

        /* Avatar del profesor */
        .avatar-profe {
            width: 72px;
            height: 72px;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        /* Animación de éxito */
        .success-container {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Textarea con contador */
        .textarea-wrapper { position: relative; }
        .char-counter {
            position: absolute;
            bottom: 10px;
            right: 14px;
            font-size: 0.72rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary-custom d-flex align-items-center gap-2" href="../index.php">
                <i class="bi bi-mortarboard-fill"></i> ISIMatch
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="dashboard-alumno.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Mi Panel
                </a>
            </div>
        </div>
    </nav>

    <main class="container my-5" style="max-width: 620px;">

        <?php if ($ya_valorada): ?>
        <!-- ===== ESTADO: YA VALORADA ===== -->
        <div class="text-center success-container py-5">
            <div class="mb-4">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:90px;height:90px;">
                    <i class="bi bi-check2-circle text-success" style="font-size:2.8rem;"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-2">¡Ya has valorado esta clase!</h2>
            <p class="text-muted mb-4">Gracias por tu opinión. Tu reseña ayuda a otros alumnos a elegir al mejor tutor.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="dashboard-alumno.php" class="btn btn-primary-custom rounded-pill px-4">
                    <i class="bi bi-grid me-2"></i>Volver al panel
                </a>
                <a href="ficha-profesor.php?id=<?php echo $clase['profesor_id']; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-person me-2"></i>Ver ficha del profesor
                </a>
            </div>
        </div>

        <?php else: ?>
        <!-- ===== FORMULARIO DE VALORACIÓN ===== -->

        <!-- Cabecera de página -->
        <div class="text-center mb-4">
            <span class="badge rounded-pill px-3 py-2 mb-2" style="background:rgba(59,179,189,0.12); color:var(--primary-color); font-weight:600;">
                <i class="bi bi-star me-1"></i> Valorar clase
            </span>
            <h1 class="fw-bold h3 mb-1">¿Cómo fue tu clase?</h1>
            <p class="text-muted small">Tu opinión es muy valiosa para la comunidad ISIMatch</p>
        </div>

        <!-- Tarjeta de información de la clase -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 clase-info-card">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-profe rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm">
                    <?php echo strtoupper(substr($clase['profe_nombre'], 0, 1)); ?>
                </div>
                <div>
                    <h5 class="fw-bold mb-1"><?php echo $profe_nombre_completo; ?></h5>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span><i class="bi bi-book me-1 text-primary-custom"></i><?php echo $materia; ?></span>
                        <span><i class="bi bi-calendar3 me-1 text-primary-custom"></i><?php echo $fecha_clase; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje de error si viene del controlador -->
        <?php if ($mensaje_error): ?>
        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?php echo htmlspecialchars($mensaje_error); ?></span>
        </div>
        <?php endif; ?>

        <!-- Formulario principal -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <form id="form-valoracion" action="../PHP/guardar_valoracion.php" method="POST" novalidate>
                <input type="hidden" name="clase_id"   value="<?php echo $clase_id; ?>">
                <input type="hidden" name="profesor_id" value="<?php echo $clase['profesor_id']; ?>">

                <!-- SELECTOR DE ESTRELLAS -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold d-block mb-3">
                        Puntuación <span class="text-danger">*</span>
                    </label>

                    <div class="star-rating-group" id="starGroup" role="radiogroup" aria-label="Puntuación de 1 a 5 estrellas">
                        <input type="radio" id="star5" name="puntuacion" value="5">
                        <label for="star5" title="Excelente"><i class="bi bi-star-fill"></i></label>

                        <input type="radio" id="star4" name="puntuacion" value="4">
                        <label for="star4" title="Muy bueno"><i class="bi bi-star-fill"></i></label>

                        <input type="radio" id="star3" name="puntuacion" value="3">
                        <label for="star3" title="Bueno"><i class="bi bi-star-fill"></i></label>

                        <input type="radio" id="star2" name="puntuacion" value="2">
                        <label for="star2" title="Regular"><i class="bi bi-star-fill"></i></label>

                        <input type="radio" id="star1" name="puntuacion" value="1">
                        <label for="star1" title="Mejorable"><i class="bi bi-star-fill"></i></label>
                    </div>

                    <p class="rating-label-text mt-3" id="ratingText">Selecciona una puntuación</p>
                    <div id="star-error" class="text-danger small d-none mt-1">
                        <i class="bi bi-exclamation-circle me-1"></i>Por favor, selecciona una puntuación.
                    </div>
                </div>

                <hr class="my-4">

                <!-- COMENTARIO -->
                <div class="mb-4">
                    <label for="comentario" class="form-label fw-bold">
                        Comentario <span class="text-muted fw-normal">(opcional)</span>
                    </label>
                    <div class="textarea-wrapper">
                        <textarea
                            id="comentario"
                            name="comentario"
                            class="form-control"
                            rows="4"
                            maxlength="500"
                            placeholder="Describe tu experiencia: ¿qué aprendiste? ¿recomendarías al profesor?"></textarea>
                        <span class="char-counter"><span id="charCount">0</span>/500</span>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="d-flex gap-3 flex-wrap">
                    <button type="submit" id="btn-enviar" class="btn btn-primary-custom rounded-pill px-4 flex-grow-1 pulse-animation">
                        <i class="bi bi-send-fill me-2"></i>Enviar valoración
                    </button>
                    <a href="dashboard-alumno.php" class="btn btn-outline-secondary rounded-pill px-4">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Nota de privacidad -->
        <p class="text-muted text-center" style="font-size: 0.78rem;">
            <i class="bi bi-shield-check me-1"></i>
            Tu reseña será pública y visible en el perfil del profesor.
        </p>

        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== TEXTOS DESCRIPTIVOS DE CADA PUNTUACIÓN =====
        const ratingTexts = {
            1: '⭐ Mejorable — No fue lo que esperaba',
            2: '⭐⭐ Regular — Podría mejorar',
            3: '⭐⭐⭐ Bueno — Clase correcta',
            4: '⭐⭐⭐⭐ Muy bueno — Lo recomendaría',
            5: '⭐⭐⭐⭐⭐ Excelente — ¡Superó mis expectativas!'
        };

        const starInputs  = document.querySelectorAll('#starGroup input[type="radio"]');
        const ratingText  = document.getElementById('ratingText');
        const starError   = document.getElementById('star-error');
        const comentario  = document.getElementById('comentario');
        const charCount   = document.getElementById('charCount');
        const formVal     = document.getElementById('form-valoracion');
        const btnEnviar   = document.getElementById('btn-enviar');

        // Actualizar texto descriptivo al cambiar estrella
        starInputs.forEach(input => {
            input.addEventListener('change', () => {
                ratingText.textContent = ratingTexts[input.value] || '';
                ratingText.style.color = 'var(--primary-color)';
                starError.classList.add('d-none');
            });
        });

        // Contador de caracteres en el textarea
        if (comentario) {
            comentario.addEventListener('input', () => {
                charCount.textContent = comentario.value.length;
            });
        }

        // Validación antes de enviar
        if (formVal) {
            formVal.addEventListener('submit', function(e) {
                const selected = document.querySelector('#starGroup input[type="radio"]:checked');
                if (!selected) {
                    e.preventDefault();
                    starError.classList.remove('d-none');
                    document.getElementById('starGroup').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                // Desactivar botón para evitar doble envío
                btnEnviar.disabled = true;
                btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            });
        }
    </script>
</body>
</html>
