<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aula Virtual Avanzada - ISIMatch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />

    <style>
      /* DISEÑO DE INMERSIÓN: Tema oscuro para centrar la atención en el contenido */
      body {
        background-color: #202124;
        color: white;
        overflow: hidden; /* Evitamos el scroll para que parezca una aplicación nativa */
        height: 100vh;
      }

      /* Layout principal: Espacio para vídeo y chat lateral */
      .main-container {
        display: flex;
        height: calc(100vh - 100px);
        padding: 15px;
        gap: 15px;
      }

      /* Contenedor del vídeo principal (Profesor o Pantalla compartida) */
      .video-profe-container {
        flex: 3;
        position: relative;
        background-color: #3c4043;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      }

      /* Miniatura flotante de la cámara del alumno */
      .mi-webcam {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 240px;
        height: 160px;
        border: 3px solid #5f6368;
        border-radius: 12px;
        background: black;
        z-index: 10;
      }

      /* PANEL DE CHAT LATERAL AVANZADO */
      .chat-lateral {
        flex: 1;
        background-color: white;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        color: #333;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      }

      /* Estilización de los mensajes en el chat */
      .item-mensaje {
        transition: opacity 0.3s ease;
      }

      /* BARRA DE CONTROLES INFERIOR */
      .controles {
        height: 100px;
        background-color: #202124;
        border-top: 1px solid #3c4043;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
      }

      /* Botones circulares de acción */
      .btn-circular {
        width: 50px; height: 50px;
        border-radius: 50%;
        border: none;
        background-color: #3c4043;
        color: white;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .btn-circular:hover { background-color: #4a4e52; transform: translateY(-2px); }
      .btn-circular.active { background-color: #3bb3bd; }
      .btn-colgar { background-color: #ea4335; width: 75px; border-radius: 25px; }

      /* Animación visual de grabación */
      .grabando-dot { color: #ea4335; animation: parpadeo 1.5s infinite; }
      @keyframes parpadeo { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    </style>
  </head>

  <body>
    <div class="main-container">
      <div class="video-profe-container shadow">
        <div class="position-absolute top-0 start-0 p-3" style="z-index: 20">
          <span class="badge bg-dark bg-opacity-75 px-3 py-2 rounded-pill">
            <i class="bi bi-record-fill grabando-dot me-2"></i> GRABANDO | <span id="timer">00:00:00</span>
          </span>
        </div>

        <img id="pantalla-principal" src="https://images.unsplash.com/photo-1544717305-2782549b5136?w=1200" style="width: 100%; height: 100%; object-fit: cover" />

        <div class="mi-webcam shadow-lg">
          <video id="webcam" autoplay muted style="width: 100%; height: 100%; object-fit: cover; border-radius: 9px;"></video>
          <span class="badge bg-dark bg-opacity-50 position-absolute bottom-0 start-0 m-2">Tú</span>
        </div>
      </div>

      <div class="chat-lateral shadow">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-chat-fill me-2 text-primary-custom"></i> Chat de Clase</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                    <li><a class="dropdown-item small" href="#" onclick="exportarChat()"><i class="bi bi-download me-2"></i>Exportar chat</a></li>
                    <li><a class="dropdown-item small text-danger" href="#" onclick="limpiarChat()"><i class="bi bi-trash me-2"></i>Limpiar pantalla</a></li>
                </ul>
            </div>
        </div>

        <div class="px-3 py-2 border-bottom bg-white">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscarMensaje" class="form-control border-0 shadow-none" placeholder="Buscar en la conversación..." onkeyup="filtrarMensajes()">
            </div>
        </div>
        
        <div id="caja-chat" class="p-3 flex-grow-1 overflow-auto" style="background-color: #f8f9fa; scroll-behavior: smooth;">
          
          <div class="mb-3 item-mensaje text-center">
            <small class="badge bg-light text-muted fw-normal border">Aula iniciada a las 15:00</small>
          </div>

          <div class="mb-4 item-mensaje">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted fw-bold">Profe Isaac</small>
                <small class="text-muted" style="font-size: 0.7rem;">10:05</small>
            </div>
            <div class="p-2 bg-white border rounded-3 shadow-sm">
                ¡Hola! He subido el PDF con los ejercicios de hoy. ¿Podéis confirmarme que se abre correctamente?
            </div>
          </div>

          <div class="mb-4 item-mensaje">
            <div class="p-2 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                <div class="overflow-hidden">
                    <p class="mb-0 small fw-bold text-truncate text-dark">Ejercicios_Semana_1.pdf</p>
                    <small class="text-muted" style="font-size: 0.65rem;">Documento de lectura</small>
                </div>
                <button class="btn btn-sm btn-white ms-auto border-0 shadow-sm"><i class="bi bi-download"></i></button>
            </div>
          </div>
        </div>

        <div class="px-3 pt-2 d-flex gap-2 border-top bg-white">
            <button class="btn btn-sm btn-light text-muted" title="Adjuntar" onclick="document.getElementById('fileInput').click()"><i class="bi bi-paperclip"></i></button>
            <button class="btn btn-sm btn-light text-muted" title="Emoji" onclick="insertarEmoji('😊')">😊</button>
            <button class="btn btn-sm btn-light text-muted" title="Emoji" onclick="insertarEmoji('👍')">👍</button>
            <input type="file" id="fileInput" style="display:none" onchange="subirArchivoSimulado()">
        </div>

        <div class="p-3 bg-white">
          <div class="input-group">
            <input type="text" id="input-mensaje" class="form-control border-0 bg-light rounded-start-pill px-3" placeholder="Escribe un mensaje..." onkeypress="if(event.key==='Enter') enviarMensaje()" />
            <button class="btn btn-primary rounded-end-pill px-3" onclick="enviarMensaje()"><i class="bi bi-send-fill"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div class="controles fixed-bottom">
      <button class="btn-circular" id="btn-mic" onclick="toggleMicro(this)" title="Micrófono"><i class="bi bi-mic-fill"></i></button>
      <button class="btn-circular" id="btn-cam" onclick="toggleCamara(this)" title="Cámara"><i class="bi bi-camera-video-fill"></i></button>
      <button class="btn-circular" onclick="compartirPantalla()" title="Compartir Pantalla"><i class="bi bi-display"></i></button>
      <button class="btn-circular btn-colgar" onclick="terminar()" title="Colgar"><i class="bi bi-telephone-x-fill"></i></button>
    </div>

    <script>
/* -- LÓGICA DE WEBRTC Y MULTIMEDIA -- */
let stream = null;
let videoTrack = null;
let audioTrack = null;

// Acceso real a la cámara y micrófono del usuario
async function iniciarCamara() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    document.getElementById("webcam").srcObject = stream;
    videoTrack = stream.getVideoTracks()[0];
    audioTrack = stream.getAudioTracks()[0];
  } catch (err) {
    console.error("Acceso denegado:", err);
  }
}

// Iniciar temporizador de clase al cargar
function iniciarReloj() {
    let segundos = 0;
    setInterval(() => {
        segundos++;
        let h = Math.floor(segundos / 3600);
        let m = Math.floor((segundos % 3600) / 60);
        let s = segundos % 60;
        document.getElementById('timer').innerText = 
            `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
    }, 1000);
}

window.onload = () => {
    iniciarCamara();
    iniciarReloj();
};

// Controles de Hardware
function toggleCamara(btn) {
  if (!videoTrack) return;
  videoTrack.enabled = !videoTrack.enabled;
  btn.classList.toggle('active', !videoTrack.enabled);
  btn.innerHTML = videoTrack.enabled ? '<i class="bi bi-camera-video-fill"></i>' : '<i class="bi bi-camera-video-off-fill"></i>';
}

function toggleMicro(btn) {
  if (!audioTrack) return;
  audioTrack.enabled = !audioTrack.enabled;
  btn.classList.toggle('active', !audioTrack.enabled);
  btn.innerHTML = audioTrack.enabled ? '<i class="bi bi-mic-fill"></i>' : '<i class="bi bi-mic-mute-fill"></i>';
}

/* -- LÓGICA DE MENSAJERÍA AVANZADA -- */

// 1. Buscador de mensajes
function filtrarMensajes() {
    const busqueda = document.getElementById('buscarMensaje').value.toLowerCase();
    const mensajes = document.querySelectorAll('.item-mensaje');
    
    mensajes.forEach(msg => {
        const texto = msg.innerText.toLowerCase();
        msg.style.display = texto.includes(busqueda) ? 'block' : 'none';
    });
}

// 2. Enviar mensaje con detección de enlaces
function enviarMensaje() {
    const input = document.getElementById("input-mensaje");
    let texto = input.value.trim();
    if (texto === "") return;

    // Convertir URLs en enlaces clickables automáticamente
    const regexUrl = /(https?:\/\/[^\s]+)/g;
    texto = texto.replace(regexUrl, (url) => `<a href="${url}" target="_blank" class="text-white text-decoration-underline">${url}</a>`);

    const chat = document.getElementById("caja-chat");
    const hora = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    const div = document.createElement("div");
    div.className = "mb-3 text-end item-mensaje";
    div.innerHTML = `
        <div class="d-flex justify-content-end align-items-center mb-1 gap-2">
            <small class="text-muted" style="font-size: 0.7rem;">${hora}</small>
            <small class="text-primary fw-bold">Tú</small>
        </div>
        <div class="p-2 bg-primary text-white rounded-3 d-inline-block shadow-sm text-start" style="max-width: 85%;">
            ${texto}
        </div>
    `;
    
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight; 
    input.value = "";
}

// 3. Simulación de subida de archivos
function subirArchivoSimulado() {
    const chat = document.getElementById("caja-chat");
    const div = document.createElement("div");
    div.className = "mb-3 text-end item-mensaje";
    div.innerHTML = `
        <div class="p-2 bg-light border rounded-3 d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-file-earmark-check text-success fs-4"></i>
            <div class="text-start">
                <p class="mb-0 small fw-bold text-dark">Archivo enviado</p>
                <small class="text-muted">Documento listo</small>
            </div>
        </div>
    `;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}

function insertarEmoji(emoji) { document.getElementById("input-mensaje").value += emoji; }

function limpiarChat() {
    if(confirm("¿Borrar historial visual?")) document.getElementById("caja-chat").innerHTML = "";
}

async function compartirPantalla() {
  try {
    const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
    const video = document.createElement("video");
    video.srcObject = screenStream;
    video.autoplay = true;
    video.style.width = "100%"; video.style.height = "100%"; video.style.objectFit = "cover";
    document.getElementById("pantalla-principal").replaceWith(video);
  } catch (err) { console.log("Cancelado"); }
}

function terminar() {
  if (confirm("¿Seguro que quieres salir del aula?")) {
    if (stream) stream.getTracks().forEach(t => t.stop());
    window.location.href = "dashboard-alumno.php";
  }
}
    </script>
  </body>
</html>