#ISIMatch - Plataforma de Tutorías Online.

Bienvenido a **ISIMatch**, una aplicación web integral diseñada para conectar alumnos con profesores particulares de forma eficiente y profesional. Este proyecto ha sido desarrollado como parte del ciclo formativo de **Desarrollo de Aplicaciones Web (DAW)**.

##Tecnologías Utilizadas

- **Frontend:** HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, JS.
- **Backend:** PHP 8.x (Programación procedimental y lógica modular).
- **Base de Datos:** MySQL (Relacional).
- **Servidor:** Apache (XAMPP/Localhost).
- **Arquitectura:** Basada en el patrón **MVC** (Modelo-Vista-Controlador) para una separación clara de la lógica y la interfaz.

##--Funcionalidades Principales--

###Gestión de Usuarios

- **Registro e Inicio de Sesión Seguro:** Uso de password_hash y password_verify para la protección de credenciales.
- **Roles Diferenciados:** Paneles de control personalizados para alumnos y profesores.
- **Perfiles Dinámicos:** Fichas públicas de profesores con biografía, idiomas, tiempo de respuesta y valoraciones.

###Reservas y Clases

- **Sistema de Solicitudes:** Los alumnos pueden solicitar clases indicando materia, fecha y duración.
- **Gestión en Tiempo Real:** Los profesores pueden aceptar o rechazar solicitudes desde su panel principal.
- **Aula Virtual:** Acceso directo a sesiones confirmadas.

###Mensajería y Facturación

- **Chat Privado:** Sistema de comunicación interna con notificaciones de mensajes no leídos y scroll automático.
- **Gestión Financiera:** Control de gastos mensuales para alumnos con barra de progreso de presupuesto.
- **Generador de Facturas:** Sistema optimizado mediante CSS `@media print` para generar recibos profesionales en PDF directamente desde el navegador.

###Lógica de Cliente (JavaScript) -**Se ha implementado JavaScript nativo** para mejorar la experiencia de usuario (UX) y añadir capas de interactividad: -**Seguridad y Acceso:** Lógica de alternancia para visualizar/ocultar contraseñas y evaluador dinámico de fortaleza de claves en el registro. -**Interactividad en Tiempo Real:** Filtros de búsqueda instantáneos en el historial de pagos y catálogo de profesores, permitiendo localizar registros sin recargar la página. -**Gestión del DOM:** Sistema de auto-scroll en el chat para posicionar la vista en el último mensaje y control de estados en botones para evitar envíos duplicados al servidor.

##--Estructura del Proyecto--

- /PHP: Lógica de control, conexión a BD (conexion.php) y procesadores de formularios.
- /VIEWS: Archivos principales de la interfaz (dashboard, fichas, mensajes, pagos).
- /CSS: Estilos globales y configuraciones de diseño responsivo.
- /IMG: Imágen utilizada (videollamada.php).

##--Instalación y Configuración--

1. Clonar el repositorio en la carpeta `htdocs` de XAMPP.
2. Importar el archivo SQL adjunto en phpMyAdmin para crear las tablas (usuarios, profesores_detalles, clases, mensajes, etc.).
3. Configurar las credenciales de phpMyAdmin en PHP/conexion.php (por defecto son las insertadas)
4. Acceder a http://localhost:8000/isimatch/index.php. (si utilizas otro puerto diferente al ":8000" por configuracion del XAMPP, utilizar ese)

---

**Este readme fue desarrollado por:** Silvia.
**Fecha:** Mayo 2026.
