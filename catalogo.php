<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buscar Profesores - ISIMatch</title>

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

  <body>
    <nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
      <div class="container">
        <a
          class="navbar-brand fw-bold fs-3 d-flex align-items-center gap-2"
          href="index.html"
        >
          <i class="bi bi-mortarboard-fill text-primary-custom"></i>
          <span>ISIMatch</span>
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div
          class="collapse navbar-collapse justify-content-center"
          id="navbarNav"
        >
          <ul class="navbar-nav gap-3">
            <li class="nav-item">
              <a
                class="nav-link fw-bold text-primary-custom"
                href="catalogo.html"
              >
                Encontrar profesor
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="registro.html?rol=profesor">
                Convertirse en tutor
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.html#faq">Ayuda</a>
            </li>
          </ul>
        </div>

        <div class="d-flex gap-2 mt-3 mt-lg-0">
          <a
            href="login.html"
            class="btn btn-outline-custom d-flex align-items-center gap-2"
          >
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </a>
          <a href="registro.html" class="btn btn-primary-custom">Registrarse</a>
        </div>
      </div>
    </nav>

    <div class="container" style="margin-top: 100px; margin-bottom: 80px">
      <div class="row mb-5">
        <div class="col-12">
          <h2 class="mb-3 fw-bold">Profesores disponibles</h2>
          <div class="d-flex gap-2 overflow-visible pb-2 flex-wrap">
            <div class="dropdown">
              <button
                class="btn btn-outline-dark rounded-pill px-4 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                📅 Disponibilidad
              </button>
              <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item" href="#">Cualquier momento</a></li>
                <li><a class="dropdown-item" href="#">Hoy</a></li>
                <li><a class="dropdown-item" href="#">Esta semana</a></li>
                <li><a class="dropdown-item" href="#">Fines de semana</a></li>
              </ul>
            </div>

            <div class="dropdown">
              <button
                class="btn btn-outline-dark rounded-pill px-4 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                data-bs-auto-close="outside"
                aria-expanded="false"
              >
                💰 Precio
              </button>
              <div
                class="dropdown-menu shadow-sm border-0 p-3"
                style="min-width: 250px"
              >
                <label class="form-label small fw-bold text-muted mb-2"
                  >RANGO DE PRECIO (€/H)</label
                >
                <div class="d-flex align-items-center gap-2 mb-3">
                  <input
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Mín"
                    min="0"
                  />
                  <span class="text-muted">-</span>
                  <input
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Máx"
                    min="0"
                  />
                </div>
                <button class="btn btn-primary-custom btn-sm w-100">
                  Aplicar
                </button>
              </div>
            </div>

            <div class="dropdown">
              <button
                class="btn btn-outline-dark rounded-pill px-4 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                📚 Materia
              </button>
              <ul
                class="dropdown-menu shadow-sm border-0"
                style="max-height: 200px; overflow-y: auto"
              >
                <li>
                  <a class="dropdown-item" href="#">Todas las materias</a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="#">Matemáticas</a></li>
                <li><a class="dropdown-item" href="#">Programación Web</a></li>
                <li><a class="dropdown-item" href="#">Inglés</a></li>
                <li><a class="dropdown-item" href="#">Física</a></li>
                <li><a class="dropdown-item" href="#">Química</a></li>
                <li><a class="dropdown-item" href="#">Biología</a></li>
                <li><a class="dropdown-item" href="#">Historia</a></li>
              </ul>
            </div>

            <div class="dropdown">
              <button
                class="btn btn-outline-dark rounded-pill px-4 dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                🌎 Idioma
              </button>
              <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item" href="#">Español</a></li>
                <li><a class="dropdown-item" href="#">Inglés</a></li>
                <li><a class="dropdown-item" href="#">Francés</a></li>
                <li><a class="dropdown-item" href="#">Alemán</a></li>
                <li><a class="dropdown-item" href="#">Italiano</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card card-custom h-100 p-3">
            <div class="d-flex gap-3 align-items-center mb-3">
              <img
                src="https://placehold.co/80x80"
                class="rounded-circle"
                alt="Foto Profe"
              />
              <div>
                <h5 class="mb-0 fw-bold">Marcos</h5>
                <small class="text-primary-custom fw-bold"
                  >Programación web</small
                >
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i> 4.9 (24 reseñas)
                </div>
              </div>
            </div>

            <p class="text-muted small">
              Experto en Frontend. Clases dinámicas y prácticas para todos los
              niveles. HTML, CSS y JS.
            </p>

            <div
              class="mt-auto d-flex justify-content-between align-items-center"
            >
              <span class="fw-bold fs-5">
                15€<small class="text-muted fw-normal fs-6">/h</small>
              </span>
              <a href="ficha.html" class="btn btn-outline-custom btn-sm">
                Ver perfil
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card card-custom h-100 p-3">
            <div class="d-flex gap-3 align-items-center mb-3">
              <img
                src="https://placehold.co/80x80?text=S"
                class="rounded-circle"
                alt="Foto Profe"
              />
              <div>
                <h5 class="mb-0 fw-bold">Silvia</h5>
                <small class="text-primary-custom fw-bold">Matemáticas</small>
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i> 5.0 (12 reseñas)
                </div>
              </div>
            </div>
            <p class="text-muted small">
              Ingeniera industrial. Te ayudo a aprobar cálculo y álgebra sin
              sufrir. ESO y Bachillerato.
            </p>
            <div
              class="mt-auto d-flex justify-content-between align-items-center"
            >
              <span class="fw-bold fs-5">
                20€<small class="text-muted fw-normal fs-6">/h</small>
              </span>
              <a href="#" class="btn btn-outline-custom btn-sm"> Ver perfil </a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card card-custom h-100 p-3">
            <div class="d-flex gap-3 align-items-center mb-3">
              <img
                src="https://placehold.co/80x80?text=I"
                class="rounded-circle"
                alt="Foto Profe"
              />
              <div>
                <h5 class="mb-0 fw-bold">Isaac</h5>
                <small class="text-primary-custom fw-bold">Inglés B2/C1</small>
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i> 4.8 (40 reseñas)
                </div>
              </div>
            </div>
            <p class="text-muted small">
              Profesor nativo. Preparación para exámenes Cambridge y
              conversación fluida.
            </p>
            <div
              class="mt-auto d-flex justify-content-between align-items-center"
            >
              <span class="fw-bold fs-5">
                18€<small class="text-muted fw-normal fs-6">/h</small>
              </span>
              <a href="#" class="btn btn-outline-custom btn-sm"> Ver perfil </a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card card-custom h-100 p-3">
            <div class="d-flex gap-3 align-items-center mb-3">
              <img
                src="https://placehold.co/80x80?text=H"
                class="rounded-circle"
                alt="Foto Profe"
              />
              <div>
                <h5 class="mb-0 fw-bold">Hugo</h5>
                <small class="text-primary-custom fw-bold">Física</small>
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i> 4.7 (15 reseñas)
                </div>
              </div>
            </div>
            <p class="text-muted small">
              Entiende las leyes del universo de forma sencilla. Mecánica y
              Electromagnetismo.
            </p>
            <div
              class="mt-auto d-flex justify-content-between align-items-center"
            >
              <span class="fw-bold fs-5">
                16€<small class="text-muted fw-normal fs-6">/h</small>
              </span>
              <a href="#" class="btn btn-outline-custom btn-sm"> Ver perfil </a>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card card-custom h-100 p-3">
            <div class="d-flex gap-3 align-items-center mb-3">
              <img
                src="https://placehold.co/80x80?text=Isabel"
                class="rounded-circle"
                alt="Foto Profe"
              />
              <div>
                <h5 class="mb-0 fw-bold">Isabel</h5>
                <small class="text-primary-custom fw-bold">Química</small>
                <div class="text-warning small">
                  <i class="bi bi-star-fill"></i> 5.0 (8 reseñas)
                </div>
              </div>
            </div>
            <p class="text-muted small">
              Clases de apoyo para selectividad y primeros cursos de carrera.
            </p>
            <div
              class="mt-auto d-flex justify-content-between align-items-center"
            >
              <span class="fw-bold fs-5">
                19€<small class="text-muted fw-normal fs-6">/h</small>
              </span>
              <a href="#" class="btn btn-outline-custom btn-sm"> Ver perfil </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
