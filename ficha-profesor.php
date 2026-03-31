<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

// Capturamos el ID de la URL. Si no hay, usamos el de la sesión actual.
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $perfil_id = $conn->real_escape_string($_GET['id']);
} else {
    $perfil_id = $_SESSION['usuario_id'];
}

$sql = "SELECT u.nombre, u.apellidos, pd.titulo_profesional, pd.bio, pd.tarifa_hora, pd.valoracion_media 
        FROM usuarios u 
        LEFT JOIN profesores_detalles pd ON u.id = pd.usuario_id 
        WHERE u.id = '$perfil_id'";

$resultado = $conn->query($sql);
$profe = $resultado->fetch_assoc();

if (!$profe) {
    die("Perfil no encontrado. <a href='catalogo.php'>Volver al catálogo</a>");
}

$nombre_profe = htmlspecialchars($profe['nombre'] . " " . $profe['apellidos']);
$es_mi_perfil = ($_SESSION['usuario_id'] == $perfil_id);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Perfil de <?php echo $nombre_profe; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="bg-light">
    <nav class="navbar navbar-light bg-white border-bottom sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="catalogo.php">
          <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
      </div>
    </nav>

    <div class="container my-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 mb-4 text-center text-md-start">
                    <h1 class="fw-bold"><?php echo $nombre_profe; ?></h1>
                    <p class="text-primary-custom fw-bold fs-5"><?php echo htmlspecialchars($profe['titulo_profesional'] ?? 'Profesor'); ?></p>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($profe['bio'] ?? 'Sin biografía disponible.')); ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 text-center">
                    <h3 class="fw-bold"><?php echo number_format($profe['tarifa_hora'] ?? 15, 2); ?>€ / h</h3>
                    <div class="d-grid gap-2 mt-4">
                        <?php if(!$es_mi_perfil): ?>
                            <button class="btn btn-primary-custom py-3 fw-bold">Reservar Clase</button>
                        <?php else: ?>
                            <a href="configuracion-profesor.php" class="btn btn-dark py-3">Editar mi Perfil</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>