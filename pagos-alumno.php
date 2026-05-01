<?php
session_start();

// ESCUDO DE SEGURIDAD: Solo alumnos logueados
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'alumno') {
    header("Location: login.php");
    exit();
}

// Preparamos los datos del alumno
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
$inicial = strtoupper(substr($nombre_usuario, 0, 1));
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pagos y Facturación - ISIMatch</title>

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

  <body class="bg-light">
    <nav class="navbar navbar-light bg-white border-bottom sticky-top">
      <div class="container">
        <!-- Enlace corregido a .php -->
        <a
          class="navbar-brand fw-bold d-flex align-items-center gap-2"
          href="dashboard-alumno.php"
        >
          <i class="bi bi-arrow-left-circle text-muted"></i>
          <span>Volver al Panel</span>
        </a>

        <div class="dropdown">
          <a
            class="nav-link dropdown-toggle d-flex align-items-center gap-2"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
          >
            <!-- Inicial dinámica en el avatar -->
            <div class="rounded-circle border d-flex align-items-center justify-content-center bg-warning text-white fw-bold" style="width:40px; height:40px;">
                <?php echo $inicial; ?>
            </div>
            <span><?php echo $nombre_usuario; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
            <li>
              <a class="dropdown-item" href="ficha-alumno.php">Mi perfil</a>
            </li>
            <li>
              <a class="dropdown-item active" href="#">Pagos y facturación</a>
            </li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <!-- Enlace de salida corregido -->
              <a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container my-5">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Pagos y Facturación</h2>
        <button class="btn btn-outline-custom">
          <i class="bi bi-download"></i> Descargar todo
        </button>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-4">
              <i class="bi bi-wallet2"></i> Métodos de Pago
            </h5>

            <div
              class="p-3 border border-primary border-2 rounded bg-light mb-3 position-relative"
            >
              <span class="badge bg-primary position-absolute top-0 end-0 m-2"
                >Principal</span
              >
              <div class="d-flex align-items-center gap-3">
                <i
                  class="bi bi-credit-card-2-front fs-1 text-primary-custom"
                ></i>
                <div>
                  <p class="mb-0 fw-bold">Visa terminada en 4242</p>
                  <small class="text-muted">Expira 12/26</small>
                </div>
              </div>
              <div class="mt-3 text-end">
                <button
                  class="btn btn-sm btn-link text-muted text-decoration-none"
                >
                  Editar
                </button>
              </div>
            </div>

            <div class="p-3 border rounded mb-3">
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-credit-card-2-front fs-1 text-muted"></i>
                <div>
                  <p class="mb-0 fw-bold text-muted">
                    Mastercard terminada en 8899
                  </p>
                  <small class="text-muted">Expira 08/25</small>
                </div>
              </div>
              <div class="mt-3 text-end">
                <button
                  class="btn btn-sm btn-link text-muted text-decoration-none"
                >
                  Hacer principal
                </button>
                <button
                  class="btn btn-sm btn-link text-danger text-decoration-none"
                >
                  Eliminar
                </button>
              </div>
            </div>

            <button class="btn btn-outline-custom w-100 py-2 border-dashed">
              <i class="bi bi-plus-circle"></i> Añadir nuevo método de pago
            </button>
          </div>

          <div class="card card-custom p-4 bg-primary-custom text-white">
            <h6 class="opacity-75">Crédito disponible para clases</h6>
            <h2 class="fw-bold mb-0">0,00 €</h2>
            <small class="opacity-75"
              >Recarga saldo para reservar más rápido.</small
            >
            <button
              class="btn btn-light text-primary-custom fw-bold mt-3 btn-sm"
            >
              Recargar Saldo
            </button>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4">Historial de Transacciones</h5>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="text-muted small">FECHA</th>
                    <th scope="col" class="text-muted small">CONCEPTO</th>
                    <th scope="col" class="text-muted small">PROFESOR</th>
                    <th scope="col" class="text-muted small">IMPORTE</th>
                    <th scope="col" class="text-muted small">ESTADO</th>
                    <th scope="col" class="text-muted small text-end">
                      FACTURA
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>10 Dic 2023</td>
                    <td class="fw-bold">Clase de Programación Web</td>
                    <td>Marcos Gómez</td>
                    <td>15,00 €</td>
                    <td>
                      <span class="badge bg-success bg-opacity-10 text-success"
                        >Pagado</span
                      >
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light border">
                        <i class="bi bi-download"></i> PDF
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>05 Dic 2023</td>
                    <td class="fw-bold">Clase de Matemáticas</td>
                    <td>Silvia P.</td>
                    <td>20,00 €</td>
                    <td>
                      <span class="badge bg-success bg-opacity-10 text-success"
                        >Pagado</span
                      >
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light border">
                        <i class="bi bi-download"></i> PDF
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>28 Nov 2023</td>
                    <td class="fw-bold">Pack 5 Clases Inglés</td>
                    <td>Isaac Ruiz</td>
                    <td>85,00 €</td>
                    <td>
                      <span class="badge bg-success bg-opacity-10 text-success"
                        >Pagado</span
                      >
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-light border">
                        <i class="bi bi-download"></i> PDF
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <nav class="mt-3">
              <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                  <a class="page-link" href="#">Anterior</a>
                </li>
                <li class="page-item active">
                  <a
                    class="page-link bg-primary-custom border-primary-custom"
                    href="#"
                    >1</a
                  >
                </li>
                <li class="page-item">
                  <a class="page-link text-primary-custom" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link text-primary-custom" href="#">3</a>
                </li>
                <li class="page-item">
                  <a class="page-link text-primary-custom" href="#"
                    >Siguiente</a
                  >
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>