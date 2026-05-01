<?php
session_start(); // Iniciamos o recuperamos la sesión

// ESCUDO DE SEGURIDAD: 
// Comprobamos si NO hay una sesión iniciada o si el rol NO es de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    // Si no es un profesor logueado, lo expulsamos al login
    header("Location: login.php");
    exit();
}

// Preparamos la inicial para el avatar del menú
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Profesor';
$inicial = strtoupper(substr($nombre_usuario, 0, 1));
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Profesor - ISIMatch</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="style.css" />

    <style>
      .calendario-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        margin-top: 10px;
      }

      .dia-semana {
        text-align: center;
        font-weight: bold;
        color: #6c757d;
        font-size: 0.75rem;
      }

      .dia-mes {
        aspect-ratio: 1 / 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        font-size: 0.8rem;
      }

      .dia-mes:hover {
        background-color: #e9ecef;
      }

      .dia-mes.hoy {
        background-color: #3bb3bd;
        color: white;
        font-weight: bold;
      }

      .punto-amarillo {
        width: 4px;
        height: 4px;
        background-color: #ffc107;
        border-radius: 50%;
        position: absolute;
        bottom: 3px;
      }

      /* Estilo para el avatar de iniciales */
      .avatar-inicial {
        width: 40px;
        height: 40px;
        background-color: #FFC947;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 50%;
      }
    </style>
  </head>

  <body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a
          class="navbar-brand fw-bold d-flex align-items-center gap-2"
          href="index.php"
        >
          <i class="bi bi-mortarboard-fill text-primary-custom"></i>
          ISIMatch
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarDashboard"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarDashboard">
          <ul class="navbar-nav ms-auto align-items-center gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold active text-primary-custom" href="dashboard-profesor.php">
                <i class="bi bi-grid-fill"></i> Panel
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="#">
                <i class="bi bi-calendar3"></i> Mi Calendario
              </a>
            </li>

            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-chat-dots"></i> Mensajes
                <span class="badge bg-danger rounded-pill">2</span>
              </a>

              <ul
                class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                style="width: 320px"
              >
                <li>
                  <h6 class="dropdown-header fw-bold">Mensajes recientes</h6>
                </li>

                <li>
                  <a
                    class="dropdown-item d-flex align-items-center gap-3 py-2"
                    href="#"
                  >
                    <img
                      src="https://placehold.co/40x40"
                      class="rounded-circle"
                      alt="Ana"
                    />
                    <div class="overflow-hidden">
                      <div class="d-flex justify-content-between small">
                        <strong class="text-dark">Ana García</strong>
                        <span class="text-muted" style="font-size: 0.75rem"
                          >16:05</span
                        >
                      </div>
                      <div class="text-muted text-truncate small">
                        Hola profe, ¿tengo que llevar el libro?
                      </div>
                    </div>
                  </a>
                </li>

                <li><hr class="dropdown-divider" /></li>

                <li>
                  <a
                    class="dropdown-item d-flex align-items-center gap-3 py-2"
                    href="#"
                  >
                    <img
                      src="https://placehold.co/40x40?text=L"
                      class="rounded-circle"
                      alt="Luis"
                    />
                    <div class="overflow-hidden">
                      <div class="d-flex justify-content-between small">
                        <strong class="text-dark">Luis M.</strong>
                        <span class="text-muted" style="font-size: 0.75rem"
                          >10:30</span
                        >
                      </div>
                      <div class="text-muted text-truncate small">
                        Gracias por la clase de hoy, muy útil.
                      </div>
                    </div>
                  </a>
                </li>

                <li><hr class="dropdown-divider" /></li>

                <li>
                  <a
                    class="dropdown-item text-center text-primary-custom fw-bold small py-2"
                    href="#"
                  >
                    Ver todos los mensajes
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <div class="avatar-inicial border"><?php echo $inicial; ?></div>
                <span><?php echo htmlspecialchars($nombre_usuario); ?></span>
              </a>

              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li>
                  <a class="dropdown-item" href="ficha-profesor.php">
                    <i class="bi bi-person-badge me-2"></i>Ver mi perfil público
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="configuracion-profesor.php">
                    <i class="bi bi-gear me-2"></i>Configuración
                  </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold text-dark">Panel de Control</h2>
          <p class="text-muted mb-0">Gestión de clases y disponibilidad</p>
        </div>
        <button class="btn btn-primary-custom shadow-sm">
          <i class="bi bi-plus-lg"></i> Nueva disponibilidad
        </button>
      </header>

      <div class="row g-4">
        <section class="col-lg-8">
          <article
            class="card card-custom p-4 mb-4 border-start border-4 border-info"
          >
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gap-3"
            >
              <div>
                <span class="badge bg-info bg-opacity-10 text-info mb-2"
                  >Próxima clase</span
                >
                <h4 class="mb-1">Programación Web</h4>
                <p class="text-muted mb-0">
                  <i class="bi bi-clock"></i>
                  Hoy, 17:00 - 18:00 · <strong>Juan Pérez</strong>
                </p>
              </div>
              <a href="videollamada.php" class="btn btn-primary-custom px-4">
                <i class="bi bi-camera-video-fill"></i> Entrar al aula
              </a>
            </div>
          </article>

          <article class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="fw-bold mb-0">Mis Recursos Compartidos</h5>
              <button class="btn btn-outline-custom btn-sm">
                <i class="bi bi-upload"></i> Subir archivo
              </button>
            </div>

            <div class="list-group list-group-flush">
              <div
                class="list-group-item px-0 d-flex justify-content-between align-items-center"
              >
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-light p-2 rounded text-danger">
                    <i class="bi bi-file-earmark-pdf fs-4"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">Temario_Tema_1.pdf</h6>
                    <small class="text-muted">Subido el 10 Dic • 2.4 MB</small>
                  </div>
                </div>
                <button class="btn btn-sm text-muted">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
              </div>

              <div
                class="list-group-item px-0 d-flex justify-content-between align-items-center"
              >
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-light p-2 rounded text-primary">
                    <i class="bi bi-file-earmark-word fs-4"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">Ejercicios_Javascript.docx</h6>
                    <small class="text-muted">Subido el 08 Dic • 500 KB</small>
                  </div>
                </div>
                <button class="btn btn-sm text-muted">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
              </div>

              <div
                class="list-group-item px-0 d-flex justify-content-between align-items-center"
              >
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-light p-2 rounded text-success">
                    <i class="bi bi-file-earmark-zip fs-4"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">Proyecto_Final.zip</h6>
                    <small class="text-muted">Subido el 01 Dic • 15 MB</small>
                  </div>
                </div>
                <button class="btn btn-sm text-muted">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
              </div>
            </div>
          </article>
        </section>

        <aside class="col-lg-4">
          <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0 fw-bold" id="tituloCalendario">Cargando...</h6>

              <div class="btn-group btn-group-sm">
                <button
                  class="btn btn-outline-secondary p-0 px-2"
                  onclick="cambiarMes(-1)"
                >
                  <i class="bi bi-chevron-left small"></i>
                </button>
                <button
                  class="btn btn-outline-secondary p-0 px-2"
                  onclick="irAHoy()"
                >
                  Hoy
                </button>
                <button
                  class="btn btn-outline-secondary p-0 px-2"
                  onclick="cambiarMes(1)"
                >
                  <i class="bi bi-chevron-right small"></i>
                </button>
              </div>
            </div>

            <div class="bg-white">
              <div
                class="d-flex justify-content-between text-muted small mb-2 text-center"
              >
                <div style="width: 14%">Lu</div>
                <div style="width: 14%">Ma</div>
                <div style="width: 14%">Mi</div>
                <div style="width: 14%">Ju</div>
                <div style="width: 14%">Vi</div>
                <div style="width: 14%">Sa</div>
                <div style="width: 14%">Do</div>
              </div>

              <div id="contenedorDias" class="calendario-grid"></div>
            </div>

            <div class="mt-3 text-center">
              <small class="text-muted">
                <span
                  class="d-inline-block rounded-circle bg-warning"
                  style="width: 6px; height: 6px"
                ></span>
                Clase pendiente
              </small>
            </div>
          </div>

          <div class="card card-custom p-4 sticky-top" style="top: 20px">
            <h5 class="mb-4 fw-bold">
              Solicitudes
              <span class="badge bg-warning text-dark rounded-pill">2</span>
            </h5>

            <div class="solicitud p-3 bg-light border rounded mb-3" data-id="1">
              <div class="d-flex gap-3 mb-2">
                <img src="https://placehold.co/40x40" class="rounded-circle" />
                <div>
                  <h6 class="mb-0 fw-bold">Ana García</h6>
                  <small class="text-muted">Matemáticas</small>
                </div>
              </div>
              <div class="small text-muted mb-3 d-flex gap-3">
                <span><i class="bi bi-calendar"></i> 12 Dic</span>
                <span><i class="bi bi-clock"></i> 16:00</span>
              </div>
              <div class="d-flex gap-2">
                <button
                  class="btn btn-success btn-sm w-50 fw-bold text-white"
                  onclick="aceptarClase(this)"
                >
                  Aceptar
                </button>
                <button
                  class="btn btn-outline-secondary btn-sm w-50 bg-white"
                  onclick="rechazarClase(this)"
                >
                  Rechazar
                </button>
              </div>
            </div>

            <div class="solicitud p-3 bg-light border rounded" data-id="2">
              <div class="d-flex gap-3 mb-2">
                <img
                  src="https://placehold.co/40x40?text=L"
                  class="rounded-circle"
                />
                <div>
                  <h6 class="mb-0 fw-bold">Luis M.</h6>
                  <small class="text-muted">Inglés</small>
                </div>
              </div>
              <div class="small text-muted mb-3 d-flex gap-3">
                <span><i class="bi bi-calendar"></i> 14 Dic</span>
                <span><i class="bi bi-clock"></i> 10:00</span>
              </div>
              <div class="d-flex gap-2">
                <button
                  class="btn btn-success btn-sm w-50 fw-bold text-white"
                  onclick="aceptarClase(this)"
                >
                  Aceptar
                </button>
                <button
                  class="btn btn-outline-secondary btn-sm w-50 bg-white"
                  onclick="rechazarClase(this)"
                >
                  Rechazar
                </button>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // VARIABLES GLOBALES
      let fechaActual = new Date();
      const diasConClase = [12, 14, 28]; // Los días con punto amarillo

      // FUNCIONES LÓGICAS DEL CALENDARIO
      function cargarCalendario() {
        const titulo = document.getElementById("tituloCalendario");
        const contenedor = document.getElementById("contenedorDias");
        contenedor.innerHTML = "";

        const anio = fechaActual.getFullYear();
        const mes = fechaActual.getMonth();

        const nombresMeses = [
          "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
          "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
        ];
        titulo.textContent = `${nombresMeses[mes]} ${anio}`;

        const primerDiaSemana = new Date(anio, mes, 1).getDay();
        const totalDiasMes = new Date(anio, mes + 1, 0).getDate();

        let empezarEn = primerDiaSemana === 0 ? 6 : primerDiaSemana - 1;

        for (let i = 0; i < empezarEn; i++) {
          const espacio = document.createElement("div");
          contenedor.appendChild(espacio);
        }

        const hoy = new Date();

        for (let dia = 1; dia <= totalDiasMes; dia++) {
          const nuevoDia = document.createElement("div");
          nuevoDia.classList.add("dia-mes");
          nuevoDia.textContent = dia;

          if (
            dia === hoy.getDate() &&
            mes === hoy.getMonth() &&
            anio === hoy.getFullYear()
          ) {
            nuevoDia.classList.add("hoy");
          }

          if (diasConClase.includes(dia)) {
            const punto = document.createElement("div");
            punto.classList.add("punto-amarillo");
            nuevoDia.appendChild(punto);
          }

          contenedor.appendChild(nuevoDia);
        }
      }

      function cambiarMes(valor) {
        fechaActual.setMonth(fechaActual.getMonth() + valor);
        cargarCalendario();
      }

      function irAHoy() {
        fechaActual = new Date();
        cargarCalendario();
      }

      document.addEventListener("DOMContentLoaded", cargarCalendario);

      function aceptarClase(boton) {
        const solicitud = boton.closest(".solicitud");
        solicitud.innerHTML =
          "<div class='py-4 text-center text-success fw-bold'><i class='bi bi-check-circle-fill'></i> Clase aceptada</div>";
        setTimeout(() => solicitud.remove(), 1500);
      }

      function rechazarClase(boton) {
        if (confirm("¿Seguro que quieres rechazar esta solicitud?")) {
          const solicitud = boton.closest(".solicitud");
          solicitud.style.opacity = "0.5";
          setTimeout(() => solicitud.remove(), 500);
        }
      }
    </script>
  </body>
</html>