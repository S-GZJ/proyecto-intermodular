<?php
//Iniciamos o recuperamos la sesión para identificar al profesor logueado
session_start(); 

/*--ESCUDO DE SEGURIDAD--
Verificamos que el usuario tenga una sesión activa Y que su rol sea 'profesor'
Si no cumple, se le redirige al login para proteger la información del panel
*/
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: login.php");
    exit();
}

//Preparamos el nombre real del profesor para la interfaz y generamos su inicial para el avatar
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Profesor';
$inicial = strtoupper(substr($nombre_usuario, 0, 1));
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Profesor - ISIMatch</title>

    <!--Librerías de estilos bootstrap para estructura y bootstrap icons para la iconografía-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />

    <style>
      /*DISEÑO DEL CALENDARIO: Usamos CSS Grid para crear una cuadrícula perfecta de 7 columnas (días)*/
      .calendario-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        margin-top: 10px;
      }

      /*Estilo para los círculos de los días del mes*/
      .dia-mes {
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        font-size: 0.8rem;
        transition: background 0.3s;
      }

      .dia-mes:hover { background-color: #e9ecef; }

      /*Clase para resaltar el día actual*/
      .dia-mes.hoy {
        background-color: #3bb3bd;
        color: white;
        font-weight: bold;
      }

      /*Punto de notificación para días que tienen clases programadas*/
      .punto-amarillo {
        width: 4px; height: 4px;
        background-color: #ffc107;
        border-radius: 50%;
        position: absolute;
        bottom: 3px;
      }

      /*Avatar circular personalizado con la inicial del profesor*/
      .avatar-inicial {
        width: 40px; height: 40px;
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
    <!--BARRA DE NAVEGACIÓN SUPERIOR-->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
          <i class="bi bi-mortarboard-fill text-primary-custom"></i> ISIMatch
        </a>

        <div class="collapse navbar-collapse" id="navbarDashboard">
          <ul class="navbar-nav ms-auto align-items-center gap-3">
            <li class="nav-item">
              <a class="nav-link fw-bold active text-primary-custom" href="dashboard-profesor.php">
                <i class="bi bi-grid-fill"></i> Panel
              </a>
            </li>

            <!--MENÚ DE USUARIO DINÁMICO-->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                <div class="avatar-inicial border"><?php echo $inicial; ?></div>
                <span><?php echo htmlspecialchars($nombre_usuario); ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                <li><a class="dropdown-item" href="ficha-profesor.php"><i class="bi bi-person-badge me-2"></i>Mi perfil público</a></li>
                <li><a class="dropdown-item" href="configuracion-profesor.php"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container my-5">
      <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold text-dark">Bienvenido/a, <?php echo htmlspecialchars($nombre_usuario); ?></h2>
          <p class="text-muted mb-0">Gestión de clases y disponibilidad</p>
        </div>
      </header>

      <div class="row g-4">
        <!--COLUMNA PRINCIPAL (Izquierda)-->
        <section class="col-lg-8">
          <!--ACCESO AL AULA VIRTUAL: Muestra la próxima clase inmediata-->
          <article class="card card-custom p-4 mb-4 border-start border-4 border-info shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div>
                <span class="badge bg-info bg-opacity-10 text-info mb-2">PRÓXIMA CLASE</span>
                <h4 class="mb-1">Programación Web</h4>
                <p class="text-muted mb-0"><i class="bi bi-clock"></i> Hoy, 17:00 - 18:00 · <strong>Juan Pérez</strong></p>
              </div>
              <a href="videollamada.php" class="btn btn-primary-custom px-4"><i class="bi bi-camera-video-fill"></i> Entrar al aula</a>
            </div>
          </article>

          <!--GESTIÓN DE ARCHIVOS: Recursos que el profesor comparte con sus alumnos-->
          <article class="card card-custom p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-4">Mis Recursos Compartidos</h5>
            <div class="list-group list-group-flush">
              <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 mb-2">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-light p-2 rounded text-danger"><i class="bi bi-file-earmark-pdf fs-4"></i></div>
                  <div>
                    <h6 class="mb-0 fw-bold">Temario_Tema_1.pdf</h6>
                    <small class="text-muted">10 Dic • 2.4 MB</small>
                  </div>
                </div>
                <button class="btn btn-sm text-muted"><i class="bi bi-three-dots-vertical"></i></button>
              </div>
            </div>
          </article>
        </section>

        <!--COLUMNA LATERAL (Derecha)-->
        <aside class="col-lg-4">
          <!--CALENDARIO DINÁMICO: Renderizado mediante JavaScript-->
          <div class="card card-custom p-4 mb-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0 fw-bold" id="tituloCalendario">Mes Año</h6>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" onclick="cambiarMes(-1)"><i class="bi bi-chevron-left"></i></button>
                <button class="btn btn-outline-secondary" onclick="irAHoy()">Hoy</button>
                <button class="btn btn-outline-secondary" onclick="cambiarMes(1)"><i class="bi bi-chevron-right"></i></button>
              </div>
            </div>
            <div id="contenedorDias" class="calendario-grid"></div>
          </div>

          <!--GESTIÓN DE SOLICITUDES: Clases nuevas que el profesor debe aceptar o rechazar-->
          <div class="card card-custom p-4 shadow-sm border-0">
            <h5 class="mb-4 fw-bold">Solicitudes <span class="badge bg-warning text-dark rounded-pill">2</span></h5>
            <div class="solicitud p-3 bg-light border rounded">
              <div class="d-flex gap-3 mb-2">
                <img src="https://placehold.co/40x40" class="rounded-circle" />
                <div><h6 class="mb-0 fw-bold">Ana García</h6><small class="text-muted">Matemáticas</small></div>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-success btn-sm w-50" onclick="aceptarClase(this)">Aceptar</button>
                <button class="btn btn-outline-secondary btn-sm w-50" onclick="rechazarClase(this)">Rechazar</button>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      /*--LÓGICA DEL CALENDARIO (Frontend)--
      Esta sección genera los días del mes actual dinámicamente
      */
      let fechaActual = new Date();
      const diasConClase = [12, 14, 28]; //Días simulados con reservas

      function cargarCalendario() {
        const titulo = document.getElementById("tituloCalendario");
        const contenedor = document.getElementById("contenedorDias");
        contenedor.innerHTML = ""; //Limpiamos el calendario

        const anio = fechaActual.getFullYear();
        const mes = fechaActual.getMonth();
        const nombresMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        titulo.textContent = `${nombresMeses[mes]} ${anio}`;

        //Cálculo de días para posicionar el primer día del mes correctamente en la cuadrícula
        const primerDiaSemana = new Date(anio, mes, 1).getDay();
        const totalDiasMes = new Date(anio, mes + 1, 0).getDate();
        let empezarEn = primerDiaSemana === 0 ? 6 : primerDiaSemana - 1;

        //Rellenamos huecos vacíos del mes anterior
        for (let i = 0; i < empezarEn; i++) { contenedor.appendChild(document.createElement("div")); }

        //Creamos cada día del mes
        const hoy = new Date();
        for (let dia = 1; dia <= totalDiasMes; dia++) {
          const nuevoDia = document.createElement("div");
          nuevoDia.classList.add("dia-mes");
          nuevoDia.textContent = dia;

          //Marcamos el día actual
          if (dia === hoy.getDate() && mes === hoy.getMonth() && anio === hoy.getFullYear()) {
             nuevoDia.classList.add("hoy");
          }

          //Añadimos punto si el día tiene clase
          if (diasConClase.includes(dia)) {
            const punto = document.createElement("div");
            punto.classList.add("punto-amarillo");
            nuevoDia.appendChild(punto);
          }
          contenedor.appendChild(nuevoDia);
        }
      }

      //Funciones de control de flujo -- v = valor 
      function cambiarMes(v) { fechaActual.setMonth(fechaActual.getMonth() + v); cargarCalendario(); }
      function irAHoy() { fechaActual = new Date(); cargarCalendario(); }
      
      //Simulación de interacción con solicitudes -- b = boton 
      function aceptarClase(b) { 
        b.closest(".solicitud").innerHTML = "<div class='text-center text-success fw-bold'>¡Aceptada!</div>";
      }

      document.addEventListener("DOMContentLoaded", cargarCalendario);
    </script>
  </body>
</html>