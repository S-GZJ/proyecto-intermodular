<?php
/**
 * ISIMatch - Gestión de Pagos y Facturación
 * Este archivo permite al alumno ver su historial de transacciones, 
 * controlar su presupuesto mensual y gestionar métodos de pago
 */

//--LÓGICA DE SERVIDOR (PHP)--

//Iniciamos la sesión si no existe una previa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../PHP/conexion.php';

/*--ESCUDO DE SEGURIDAD--*/
//Solo permitimos el acceso si el usuario es un 'alumno' logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: login.php");
    exit();
}

$alumno_id = $_SESSION['usuario_id'];
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
$inicial = strtoupper(substr($nombre_usuario, 0, 1));

//--CÁLCULO DE GASTO REAL (Mes actual) --
$mes_actual = date('m');
$anio_actual = date('Y');

/**
 * CONSULTA: SUM() Sumamos la columna 'precio_total' de todas las clases completadas
 * que hayan ocurrido dentro del mes y año actuales.
 */
$sql_gasto = "SELECT SUM(precio_total) as total FROM clases 
              WHERE alumno_id = '$alumno_id' 
              AND estado = 'completada' 
              AND MONTH(fecha_hora) = '$mes_actual' 
              AND YEAR(fecha_hora) = '$anio_actual'";

$res_gasto = $conn->query($sql_gasto);
$gasto_data = $res_gasto->fetch_assoc();
$gasto_real = $gasto_data['total'] ?? 0;

//Definimos un límite ficticio para la barra de progreso
$limite_presupuesto = 150.00;
$porcentaje_gasto = ($limite_presupuesto > 0) ? ($gasto_real / $limite_presupuesto) * 100 : 0;
//Evitamos que la barra de progreso se salga del 100%
if($porcentaje_gasto > 100) $porcentaje_gasto = 100;

//--HISTORIAL DE TRANSACCIONES--
//Traemos los datos de las clases y el nombre del profesor relacionado (JOIN)
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

    <div id="invoice-header" class="container mt-4">
        <div class="row align-items-center">
            <div class="col-7">
                <h1 style="color: #3bb3bd; font-weight: bold;">ISIMatch</h1>
                <p class="mb-0"><strong>IES LA HONTANILLA, ISIMatch S.L.</strong></p>
                <p class="small text-muted mb-0">C/ Hontanilla 123, Tarancón</p>
                <p class="small text-muted">CIF: B-12345678</p>
            </div>
            <div class="col-5 text-end">
                <h3 class="fw-bold">FACTURA / RECIBO</h3>
                <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></p>
                <p class="mb-0"><strong>Cliente:</strong> <?php echo $nombre_usuario; ?></p>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-light bg-white border-bottom sticky-top shadow-sm no-print">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard-alumno.php">
          <i class="bi bi-arrow-left-circle text-primary-custom"></i> 
          <span class="small">Volver al Panel</span>
        </a>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
            <div class="rounded-circle bg-primary-custom text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                <?php echo $inicial; ?>
            </div>
            <span class="fw-bold d-none d-md-inline text-dark"><?php echo $nombre_usuario; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3">
            <li><a class="dropdown-item py-2" href="ficha-alumno.php"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
            <li><a class="dropdown-item py-2" href="dashboard-alumno.php"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><hr class="dropdown-divider" /></li>
            <li><a class="dropdown-item text-danger py-2" href="../PHP/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <div class="row mb-4 align-items-end">
          <div class="col-md-8">
              <h2 class="fw-bold mb-0">Gestión de Pagos</h2>
              <p class="text-muted">Revisa tu historial de gastos y gestiona tus facturas.</p>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0 no-print">
              <button onclick="window.print()" class="btn btn-white bg-white border px-4 shadow-sm fw-bold">
                  <i class="bi bi-printer me-2 text-primary-custom"></i>Imprimir Factura
              </button>
          </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card card-custom shadow-sm p-4 mb-4 bg-white">
              <h6 class="fw-bold mb-3 small text-muted text-uppercase">Gasto de <?php echo date('F'); ?></h6>
              <div class="d-flex justify-content-between mb-1 small">
                  <span>Invertido: <strong><?php echo number_format($gasto_real, 2); ?>€</strong></span>
                  <span class="fw-bold text-primary-custom"><?php echo round($porcentaje_gasto); ?>%</span>
              </div>
              <div class="progress mb-2" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar bg-primary-custom" style="width: <?php echo $porcentaje_gasto; ?>%;"></div>
              </div>
              <small class="text-muted small">Límite mensual: <?php echo $limite_presupuesto; ?>€</small>
          </div>

          <div class="card card-custom shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-bold mb-4"><i class="bi bi-credit-card text-primary-custom me-2"></i>Método de Pago</h5>
            
            <?php
            //Consultamos las tarjetas guardadas del alumno
            $sql_cards = "SELECT * FROM metodos_pago WHERE usuario_id = '$alumno_id' ORDER BY predeterminada DESC";
            $res_cards = $conn->query($sql_cards);

            if ($res_cards && $res_cards->num_rows > 0):
                while($card = $res_cards->fetch_assoc()): ?>
                    <div class="p-3 border rounded-4 bg-light mb-3 position-relative <?php echo ($card['predeterminada']) ? 'border-primary-custom' : ''; ?>">
                        <?php if($card['predeterminada']): ?>
                            <span class="badge bg-primary-custom position-absolute top-0 end-0 m-2" style="font-size: 0.6rem;">ACTIVA</span>
                        <?php endif; ?>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-credit-card-2-front fs-2 text-dark"></i>
                            <div>
                                <p class="mb-0 fw-bold">•••• <?php echo $card['ultimos_cuatro']; ?></p>
                                <small class="text-muted"><?php echo $card['tipo_tarjeta']; ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; 
            else: ?>
                <div class="text-center py-3 border rounded-4 border-dashed mb-3">
                    <p class="text-muted small mb-0">No hay tarjetas guardadas.</p>
                </div>
            <?php endif; ?>

            <button class="btn btn-outline-custom w-100 py-2 rounded-pill fw-bold border-2 no-print" data-bs-toggle="modal" data-bs-target="#modalTarjeta">
              <i class="bi bi-plus-circle me-1"></i> Añadir método
            </button>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card card-custom shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <h5 class="fw-bold mb-0">Historial de Transacciones</h5>
                <div class="input-group" style="max-width: 200px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search small"></i></span>
                    <input type="text" class="form-control form-control-sm bg-light border-0" placeholder="Buscar..." id="filtroPagos">
                </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr class="small text-muted text-uppercase">
                    <th class="border-0">Fecha</th>
                    <th class="border-0">Clase</th>
                    <th class="border-0">Profesor</th>
                    <th class="border-0 text-center">Importe</th>
                    <th class="border-0 text-end no-print">Acción</th>
                  </tr>
                </thead>
                <tbody id="tablaPagos">
                  <?php if($res_transacciones && $res_transacciones->num_rows > 0): ?>
                    <?php while($t = $res_transacciones->fetch_assoc()): ?>
                    <tr class="item-pago">
                      <td class="small text-muted"><?php echo date('d/m/Y', strtotime($t['fecha_hora'])); ?></td>
                      <td class="fw-bold"><?php echo htmlspecialchars($t['materia_nombre_manual']); ?></td>
                      <td class="small"><?php echo htmlspecialchars($t['profe_nombre'] . " " . $t['profe_apellidos']); ?></td>
                      <td class="fw-bold text-dark text-center"><?php echo number_format($t['precio_total'], 2); ?> €</td>
                      <td class="text-end no-print">
                        <button onclick="window.print()" class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm fw-bold">
                             Ver Recibo
                        </button>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center py-5">
                          <i class="bi bi-receipt text-muted fs-1 d-block mb-3"></i>
                          <p class="text-muted">No se han encontrado transacciones completadas.</p>
                          <a href="catalogo.php" class="btn btn-primary-custom btn-sm rounded-pill px-4 no-print">Reservar mi primera clase</a>
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div class="modal fade no-print" id="modalTarjeta" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form action="../PHP/guardar_tarjeta.php" method="POST" class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="fw-bold">Nuevo Método de Pago</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">NÚMERO DE TARJETA</label>
                <div class="input-group bg-light rounded-3 px-2">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-credit-card"></i></span>
                    <input type="text" name="numero" class="form-control bg-transparent border-0 py-2" placeholder="0000 0000 0000 0000" maxlength="16" required>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted">EXPIRACIÓN</label>
                    <input type="text" class="form-control bg-light border-0 py-2" placeholder="MM/AA" required>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted">CVV</label>
                    <input type="password" class="form-control bg-light border-0 py-2" placeholder="***" maxlength="3" required>
                </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="submit" class="btn btn-primary-custom w-100 py-2 rounded-pill fw-bold shadow-sm mt-3">GUARDAR TARJETA</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /*FILTRADO EN TIEMPO REAL
         Oculta las filas de la tabla que no coincidan con lo que el usuario escribe
         */
        document.getElementById('filtroPagos').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.item-pago').forEach(row => {
                //Si el texto de la fila incluye el valor buscado, la muestra; si no, la oculta
                row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
</body>
</html>