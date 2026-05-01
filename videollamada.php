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
      <button class="btn-circular" onclick="toggleMicro(this)">
        <i class="bi bi-mic-fill"></i>
      </button>

      <!--boton de cámara: alterna estado visualmente-->
      <button class="btn-circular" onclick="toggleCamara(this)">
        <i class="bi bi-camera-video-fill"></i>
      </button>

      <!--botón de compartir pantalla sin funcionalidad implementada-->
      <button class="btn-circular" onclick="compartirPantalla()"><i class="bi bi-display"></i></button>

      <!--botón de colgar: rojo, con confirmación -->
      <button class="btn-circular btn-colgar" onclick="terminar()">
        <i class="bi bi-telephone-x-fill"></i>
      </button>
    </div>

    <!--scripts JavaScript -->
    <script>
let stream = null;
let videoTrack = null;
let audioTrack = null;

// INICIAR cámara y micro
async function iniciarCamara() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: true,
    });

    document.getElementById("webcam").srcObject = stream;

    videoTrack = stream.getVideoTracks()[0];
    audioTrack = stream.getAudioTracks()[0];

  } catch (err) {
    console.log("Error al acceder a cámara/micro:", err);
  }
}

window.onload = iniciarCamara;

//////////////////////////////////////////////////////
// 🎥 ACTIVAR / DESACTIVAR CÁMARA
function toggleCamara(btn) {
  if (!videoTrack) return;

  videoTrack.enabled = !videoTrack.enabled;

  btn.style.backgroundColor = videoTrack.enabled
    ? "#3c4043"
    : "#ea4335";
}

//////////////////////////////////////////////////////
// 🎤 ACTIVAR / DESACTIVAR MICRO
function toggleMicro(btn) {
  if (!audioTrack) return;

  audioTrack.enabled = !audioTrack.enabled;
const icon = btn.querySelector("i");
if (audioTrack.enabled) { btn.style.backgroundColor = "#3c4043"; icon.className = "bi bi-mic-fill";
} else { btn.style.backgroundColor = "#ea4335"; icon.className = "bi bi-mic-mute-fill"; } }
  

//////////////////////////////////////////////////////
// 🖥️ COMPARTIR PANTALLA
async function compartirPantalla() {
  try {
    const screenStream = await navigator.mediaDevices.getDisplayMedia({
      video: true,
    });

    const videoPrincipal = document.querySelector(".video-profe-container img");

    // Cambiamos la imagen por el stream
    const video = document.createElement("video");
    video.srcObject = screenStream;
    video.autoplay = true;
    video.style.width = "100%";
    video.style.height = "100%";
    video.style.objectFit = "cover";

    videoPrincipal.replaceWith(video);

  } catch (err) {
    console.log("Error al compartir pantalla:", err);
  }
}

//////////////////////////////////////////////////////
// 💬 CHAT FUNCIONAL (LOCAL)
document.querySelector(".btn.btn-primary").addEventListener("click", enviarMensaje);

document.querySelector("input").addEventListener("keypress", function(e) {
  if (e.key === "Enter") enviarMensaje();
});

function enviarMensaje() {
  const input = document.querySelector("input");
  const mensaje = input.value.trim();

  if (mensaje === "") return;

  const chat = document.querySelector(".flex-grow-1");

  const nuevoMensaje = document.createElement("div");
  nuevoMensaje.className = "mb-3 text-end";

  nuevoMensaje.innerHTML = `
    <small class="text-muted">Yo - ahora</small>
    <div class="p-2 bg-primary text-white border rounded d-inline-block">
      ${mensaje}
    </div>
  `;

  chat.appendChild(nuevoMensaje);
  chat.scrollTop = chat.scrollHeight;

  input.value = "";
}

//////////////////////////////////////////////////////
// 📞 COLGAR
function terminar() {
  if (confirm("¿Seguro que quieres salir?")) {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    window.history.back();
  }
}
</script>
  </body>
</html>
