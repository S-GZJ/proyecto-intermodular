<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aula Virtual - ISIMatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />

    <style>
      /*DISEÑO DE INMERSIÓN, usamos un tema oscuro para centrar la atención en el vídeo*/
      body {
        background-color: #202124;
        color: white;
        overflow: hidden; /*Evitamos el scroll de la página para que parezca una aplicación nativa*/
      }

      /*Layout principal dividido entre el vídeo (flexible) y el chat (fijo/lateral)*/
      .main-container {
        display: flex;
        height: calc(100vh - 100px); /*Restamos el espacio de los controles inferiores*/
        padding: 15px;
        gap: 15px;
      }

      /*Contenedor del vídeo principal (Profesor o Pantalla compartida)*/
      .video-profe-container {
        flex: 3;
        position: relative;
        background-color: #3c4043;
        border-radius: 12px;
        overflow: hidden;
      }

      /*Miniatura flotante de la propia cámara del alumno*/
      .mi-webcam {
        position: absolute;
        bottom: 15px;
        right: 15px;
        width: 220px;
        height: 150px;
        border: 2px solid #5f6368;
        border-radius: 8px;
        background: black;
        z-index: 5;
      }

      /*Panel lateral de chat con diseño limpio (blanco) para lectura fácil*/
      .chat-lateral {
        flex: 1;
        background-color: white;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        color: #333;
      }

      /*BARRA DE CONTROLES, botones de hardware y colgar*/
      .controles {
        height: 100px;
        background-color: #202124;
        border-top: 1px solid #3c4043;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
      }

      /*Estilo circular moderno para botones de acción*/
      .btn-circular {
        width: 55px; height: 55px;
        border-radius: 50%;
        border: none;
        background-color: #3c4043;
        color: white;
        transition: 0.3s;
      }

      .btn-colgar { background-color: #ea4335; width: 80px; border-radius: 25px; }

      /*Animación visual que indica que la sesión se está grabando*/
      .grabando { color: #ea4335; animation: parpadeo 1.5s infinite; }
      @keyframes parpadeo {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }
    </style>
  </head>

  <body>
    <div class="main-container">
      <div class="video-profe-container shadow">
        <div class="position-absolute top-0 start-0 p-3" style="z-index: 10">
          <span class="badge bg-dark px-3 py-2">
            <i class="bi bi-dot grabando"></i> GRABANDO | <span id="timer">00:12:45</span>
          </span>
        </div>

        <img id="pantalla-principal" src="https://images.unsplash.com/photo-1544717305-2782549b5136?w=1200" style="width: 100%; height: 100%; object-fit: cover" />

        <div class="mi-webcam shadow-lg">
          <video id="webcam" autoplay muted style="width: 100%; height: 100%; object-fit: cover"></video>
          <span class="badge bg-dark position-absolute bottom-0 start-0 m-1">Tú</span>
        </div>
      </div>

      <div class="chat-lateral shadow">
        <div class="p-3 border-bottom"><h6 class="mb-0 fw-bold"><i class="bi bi-chat-left-text"></i> Chat de la clase</h6></div>
        <div id="caja-chat" class="p-3 flex-grow-1 overflow-auto" style="background-color: #f8f9fa">
          <div class="mb-3">
            <small class="text-muted">Profe - 10:05</small>
            <div class="p-2 bg-white border rounded">¿Habéis entendido el concepto de flexbox?</div>
          </div>
        </div>

        <div class="p-3 border-top">
          <div class="input-group">
            <input type="text" id="input-mensaje" class="form-control" placeholder="Escribe un mensaje..." />
            <button class="btn btn-primary" onclick="enviarMensaje()"><i class="bi bi-send"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="controles fixed-bottom">
      <button class="btn-circular" onclick="toggleMicro(this)"><i class="bi bi-mic-fill"></i></button>
      <button class="btn-circular" onclick="toggleCamara(this)"><i class="bi bi-camera-video-fill"></i></button>
      <button class="btn-circular" onclick="compartirPantalla()"><i class="bi bi-display"></i></button>
      <button class="btn-circular btn-colgar" onclick="terminar()"><i class="bi bi-telephone-x-fill"></i></button>
    </div>

    <script>
/*--LÓGICA DE WEBRTC Y MULTIMEDIA--
*/
let stream = null;
let videoTrack = null;
let audioTrack = null;

//Acceso a hardware mediante la API de MediaDevices
async function iniciarCamara() {
  try {
    //Pedimos permiso al usuario para usar cámara y micrófono
    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    //Inyectamos el flujo de datos en el elemento <video>
    document.getElementById("webcam").srcObject = stream;
    //Guardamos las pistas (tracks) para poder activarlas/desactivarlas luego
    videoTrack = stream.getVideoTracks()[0];
    audioTrack = stream.getAudioTracks()[0];
  } catch (err) {
    console.error("Acceso denegado a multimedia:", err);
  }
}

window.onload = iniciarCamara;

//Función para silenciar/activar la cámara
function toggleCamara(btn) {
  if (!videoTrack) return;
  videoTrack.enabled = !videoTrack.enabled;
  btn.style.backgroundColor = videoTrack.enabled ? "#3c4043" : "#ea4335";
}

//Función para silenciar/activar el micrófono
function toggleMicro(btn) {
  if (!audioTrack) return;
  audioTrack.enabled = !audioTrack.enabled;
  const icon = btn.querySelector("i");
  btn.style.backgroundColor = audioTrack.enabled ? "#3c4043" : "#ea4335";
  icon.className = audioTrack.enabled ? "bi bi-mic-fill" : "bi bi-mic-mute-fill";
}

//Función para simular el compartir pantalla (sustituye la imagen principal por captura de pantalla)
async function compartirPantalla() {
  try {
    const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
    const imgPrincipal = document.getElementById("pantalla-principal");
    const video = document.createElement("video");
    video.srcObject = screenStream;
    video.autoplay = true;
    video.style.width = "100%"; video.style.height = "100%"; video.style.objectFit = "cover";
    imgPrincipal.replaceWith(video);
  } catch (err) {
    console.log("Error compartiendo pantalla:", err);
  }
}

//Lógica del chat (Envío visual inmediato)
function enviarMensaje() {
  const input = document.getElementById("input-mensaje");
  const texto = input.value.trim();
  if (texto === "") return;
  const chat = document.getElementById("caja-chat");
  const div = document.createElement("div");
  div.className = "mb-3 text-end";
  div.innerHTML = `<small class="text-muted">Yo - ahora</small><div class="p-2 bg-primary text-white rounded d-inline-block">${texto}</div>`;
  chat.appendChild(div);
  chat.scrollTop = chat.scrollHeight; // Autoscroll al final
  input.value = "";
}

//Finalización segura de la llamada
function terminar() {
  if (confirm("¿Seguro que quieres salir del aula?")) {
    if (stream) stream.getTracks().forEach(t => t.stop()); // Apagamos físicamente la cámara
    window.location.href = "dashboard-alumno.php";
  }
}
    </script>
  </body>
</html>