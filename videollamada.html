<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aula Virtual - ISIMatch</title>

    <!--bootstrap componentes e iconos-->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />

    <!-- estilos de la propia pagina-->
    <style>
      body {
        background-color: #202124;
        color: white;
        overflow: hidden;
      }

      /*distribuye video y chat horizontalmente */
      .main-container {
        display: flex;
        height: calc(100vh - 100px);
        padding: 15px;
        gap: 15px;
      }

      /* contenedor del video del profesor: ocupa 3/4 del espacio */
      .video-profe-container {
        flex: 3;
        position: relative; /* Para posicionar webcam flotante */
        background-color: #3c4043;
        border-radius: 12px;
        overflow: hidden;
      }

      /* webcam del usuario: flota encima del video principal */
      .mi-webcam {
        position: absolute;
        bottom: 15px;
        right: 15px;
        width: 220px;
        height: 150px;
        border: 2px solid #5f6368;
        border-radius: 8px;
        background: black;
      }

      /*chat lateral: ocupa 1/4 del espacio, fondo blanco para contraste */
      .chat-lateral {
        flex: 1;
        background-color: white;
        border-radius: 12px;
        display: flex;
        flex-direction: column; /*elementos en columna */
        color: #333;
      }

      /*barra de controles inferior: altura fija, centrada */
      .controles {
        height: 100px;
        background-color: #202124;
        border-top: 1px solid #3c4043;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
      }

      /*botones circulares: micrófono, cámara, compartir pantalla */
      .btn-circular {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        border: none;
        background-color: #3c4043;
        color: white;
        font-size: 1.3rem;
      }

      /*botón de colgar: rojo, más ancho, bordes redondeados */
      .btn-colgar {
        background-color: #ea4335; /* Rojo Google */
        width: 80px;
        border-radius: 25px;
      }

      /*estilo para el icono de grabación: rojo con animación de parpadeo */
      .grabando {
        color: #ea4335;
        animation: parpadeo 1.5s infinite;
      }

      /*animación de parpadeo: alterna opacidad */
      @keyframes parpadeo {
        0% {
          opacity: 1;
        }
        50% {
          opacity: 0.5;
        }
        100% {
          opacity: 1;
        }
      }
    </style>
  </head>

  <body>
    <!--contenedor principal que agrupa video y chat -->
    <div class="main-container">
      <!--sección izquierda: Video del profesor -->
      <div class="video-profe-container shadow">
        <!--badge flotante: indica que se está grabando + contador de tiempo -->
        <div class="position-absolute top-0 start-0 p-3" style="z-index: 10">
          <span class="badge bg-dark px-3 py-2">
            <i class="bi bi-dot grabando"></i> GRABANDO |
            <span id="timer">00:12:45</span>
            <!--contador actualizable -->
          </span>
        </div>

        <!--imagen que simula video del profesor-->
        <img
          src="https://images.unsplash.com/photo-1544717305-2782549b5136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
          style="width: 100%; height: 100%; object-fit: cover"
        />

        <!--contenedor de webcam del usuario flotante en esquina inferior derecha-->
        <div class="mi-webcam">
          <!--elemento de video donde se mostrará el stream de la cámara -->
          <video
            id="webcam"
            autoplay
            muted
            style="width: 100%; height: 100%; object-fit: cover"
          ></video>
          <!--badge que identifica que es tu propia cámara -->
          <span class="badge bg-dark position-absolute bottom-0 start-0 m-1"
            >Tú</span
          >
        </div>
      </div>

      <!--sección derecha: chat lateral -->
      <div class="chat-lateral shadow">
        <!--header del chat -->
        <div class="p-3 border-bottom">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-chat-left-text"></i> Chat de la clase
          </h6>
        </div>

        <!--area de mensajes con scroll automático -->
        <div
          class="p-3 flex-grow-1 overflow-auto"
          style="background-color: #f8f9fa"
        >
          <!--mensaje del profesor (alineado a la izquierda) -->
          <div class="mb-3">
            <small class="text-muted">Profe - 10:05</small>
            <div class="p-2 bg-white border rounded">
              ¿Habéis entendido el concepto de flexbox?
            </div>
          </div>

          <!--mensaje del usuario (alineado a la derecha) -->
          <div class="mb-3 text-end">
            <small class="text-muted">Yo - 10:06</small>
            <div
              class="p-2 bg-primary text-white border rounded d-inline-block"
            >
              Más o menos, ¡necesito practicar!
            </div>
          </div>
        </div>

        <!--input para escribir mensajes -->
        <div class="p-3 border-top">
          <div class="input-group">
            <input
              type="text"
              class="form-control"
              placeholder="Escribe un mensaje..."
            />
            <button class="btn btn-primary"><i class="bi bi-send"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!--barra de controles fija en la parte inferior -->
    <div class="controles fixed-bottom">
      <!--boton de micrófono: alterna estado visualmente-->
      <button class="btn-circular" onclick="cambiarEstado(this)">
        <i class="bi bi-mic-fill"></i>
      </button>

      <!--boton de cámara: alterna estado visualmente-->
      <button class="btn-circular" onclick="cambiarEstado(this)">
        <i class="bi bi-camera-video-fill"></i>
      </button>

      <!--botón de compartir pantalla sin funcionalidad implementada-->
      <button class="btn-circular"><i class="bi bi-display"></i></button>

      <!--botón de colgar: rojo, con confirmación -->
      <button class="btn-circular btn-colgar" onclick="terminar()">
        <i class="bi bi-telephone-x-fill"></i>
      </button>
    </div>

    <!--scripts JavaScript -->
    <script>
      //creamos una funcion asíncrona que solicita acceso a la cámara del usuario y muestra el video en el elemento con id="webcam"
      async function iniciarCamara() {
        try {
          //solicitamos permiso para usar la cámara (solo video, sin audio)
          const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
          });

          //asigna el stream de video al elemento <video>
          document.getElementById("webcam").srcObject = stream;
        } catch (err) {
          //si hay error (no hay cámara o no se dio permiso), lo registra en consola
          console.log("No hay camara o no hay permiso");
        }
      }

      //se inicia la cámara automáticamente cuando se carga la pagina
      window.onload = iniciarCamara;

      //creamos funcion que alterna el estado visual de los botones micro y camara
      function cambiarEstado(btn) {
        //si el botón está rojo, lo pone gris
        if (btn.style.backgroundColor === "rgb(234, 67, 53)") {
          btn.style.backgroundColor = "#3c4043";
        }
        //si el botón esta gris, lo pone rojo
        else {
          btn.style.backgroundColor = "#ea4335";
        }
      }

      //creamos funcion que se ejecuta al hacer clic en el botón de colgar pidiendo confirmación antes de salir
      function terminar() {
        // mostramos confirmacion
        if (confirm("¿Seguro que quieres salir?")) {
          //si es true vuelve a la página anterior
          window.history.back();
        }
      }

      //contador de tiempo de la clase variables que almacenan minutos y segundos
      let segundos = 45;
      let minutos = 12;

      //utilizamos setInterval donde ejecuta código cada 1000ms (1 segundo) e incrementa el contador y actualiza el texto en pantalla
      setInterval(() => {
        segundos++; //aumenta 1 segundo

        //si llega a 60 seg, resetea a 0 y aumenta 1 minuto
        if (segundos > 59) {
          segundos = 0;
          minutos++;
        }

        //actualiza el texto del contador con formato HH:MM:SS
        document.getElementById("timer").innerText =
          "00:" + //horas (siempre 00 en esta implementación)
          (minutos < 10 ? "0" + minutos : minutos) + // Minutos con cero a la izquierda
          ":" +
          (segundos < 10 ? "0" + segundos : segundos); // Segundos con cero a la izquierda
      }, 1000);
    </script>
  </body>
</html>
