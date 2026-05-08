<?php
// --- LÓGICA DE SERVIDOR (PHP) ---
session_start();
include '../PHP/conexion.php';

// ESCUDO DE SEGURIDAD
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: login.php");
    exit();
}

$alumno_id = $_SESSION['usuario_id'];
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
$inicial = strtoupper(substr($nombre_usuario, 0, 1));

// 1. CÁLCULO DE GASTO REAL (Mes actual)
// Sumamos el precio_total de las clases completadas en el mes en curso
$mes_actual = date('m');
$anio_actual = date('Y');
$sql_gasto = "SELECT SUM(precio_total) as total FROM clases 
              WHERE alumno_id = '$alumno_id' 
              AND estado = 'completada' 
              AND MONTH(fecha_hora) = '$mes_actual' 
              AND YEAR(fecha_hora) = '$anio_actual'";
$res_gasto = $conn->query($sql_gasto);
$gasto_data = $res_gasto->fetch_assoc();
$gasto_real = $gasto_data['total'] ?? 0;

// Definimos un presupuesto objetivo (podría estar en la BDD, aquí lo ponemos fijo)
$limite_presupuesto = 150.00;
$porcentaje_gasto = ($gasto_real / $limite_presupuesto) * 100;
if($porcentaje_gasto > 100) $porcentaje_gasto = 100;

// 2. HISTORIAL DE TRANSACCIONES REALES
// Obtenemos todas las clases finalizadas para generar el listado de facturas
$sql_transacciones = "SELECT c.*, u.nombre as profe_nombre, u.apellidos as profe_apellidos 
                      FROM clases c
                      JOIN usuarios u ON c.profesor_id = u.id
                      WHERE c.alumno_id = '$alumno_id' AND c.estado = 'completada'
                      ORDER BY c.fecha_hora DESC";
$res_transacciones = $conn->query($sql_transacciones);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pagos y Facturación - ISIMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body class="bg-light">

    <nav class="navbar navbar-light bg-white border-bottom sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard-alumno.php">
          <i class="bi bi-arrow-left-circle"></i> <span class="small">Panel de Control</span>
        </a>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                <?php echo $inicial; ?>
            </div>
            <span class="fw-bold d-none d-md-inline"><?php echo $nombre_usuario; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3">
            <li><a class="dropdown-item" href="ficha-alumno.php"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
            <li><hr class="dropdown-divider" /></li>
            <li><a class="dropdown-item text-danger" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container my-5">
      <div class="row mb-4">
          <div class="col-12 d-md-flex justify-content-between align-items-end">
              <div>
                  <h2 class="fw-bold mb-0">Facturación</h2>
                  <p class="text-muted">Controla tus gastos y descarga tus recibos.</p>
              </div>
              <div class="btn-group mt-3 mt-md-0 shadow-sm">
                  <button onclick="window.print()" class="btn btn-white border px-3"><i class="bi bi-printer me-2"></i>Imprimir</button>
                  <button class="btn btn-white border px-3"><i class="bi bi-download me-2"></i>Exportar</button>
              </div>
          </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          
          <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
              <h6 class="fw-bold mb-3 small text-muted">PRESUPUESTO DE <?php echo strtoupper(date('F')); ?></h6>
              <div class="d-flex justify-content-between mb-1 small">
                  <span>Gasto actual: <strong><?php echo number_format($gasto_real, 2); ?>€</strong></span>
                  <span class="fw-bold text-primary"><?php echo round($porcentaje_gasto); ?>%</span>
              </div>
              <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar bg-primary" style="width: <?php echo $porcentaje_gasto; ?>%;"></div>
              </div>
              <small class="text-muted small">Límite mensual: <?php echo $limite_presupuesto; ?>€</small>
          </div>

          <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
            <h5 class="fw-bold mb-4"><i class="bi bi-wallet2 text-primary me-2"></i>Métodos de Pago</h5>
            <div class="p-3 border rounded-4 bg-light mb-4 position-relative">
              <span class="badge bg-primary position-absolute top-0 end-0 m-2">PREDETERMINADA</span>
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-credit-card-2-front fs-2 text-dark"></i>
                <div>
                  <p class="mb-0 fw-bold">•••• 4242</p>
                  <small class="text-muted">Visa Personal</small>
                </div>
              </div>
            </div>
            <button class="btn btn-outline-primary w-100 py-2 rounded-pill fw-bold border-2">
              <i class="bi bi-plus-circle me-1"></i> Nueva tarjeta
            </button>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Historial de Clases</h5>
                <div class="input-group" style="width: 200px;">
                    <input type="text" class="form-control form-control-sm bg-light border-0" placeholder="Filtrar..." id="filtroPagos">
                </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr class="small text-muted text-uppercase">
                    <th class="border-0">Fecha</th>
                    <th class="border-0">Materia</th>
                    <th class="border-0">Tutor</th>
                    <th class="border-0">Total</th>
                    <th class="border-0 text-end">Acción</th>
                  </tr>
                </thead>
                <tbody id="tablaPagos">
                  <?php if($res_transacciones->num_rows > 0): ?>
                    <?php while($t = $res_transacciones->fetch_assoc()): ?>
                    <tr class="item-pago">
                      <td class="small text-muted"><?php echo date('d/m/Y', strtotime($t['fecha_hora'])); ?></td>
                      <td class="fw-bold"><?php echo htmlspecialchars($t['materia_nombre_manual']); ?></td>
                      <td class="small"><?php echo htmlspecialchars($t['profe_nombre']); ?></td>
                      <td class="fw-bold text-dark"><?php echo number_format($t['precio_total'], 2); ?> €</td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold small">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </button>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center py-5 text-muted">Aún no tienes pagos registrados.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtro rápido de historial
        document.getElementById('filtroPagos').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.item-pago').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
</body>
</html>