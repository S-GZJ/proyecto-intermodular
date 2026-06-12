-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-05-2026 a las 15:44:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `isimatch`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos_detalles`
--

CREATE TABLE `alumnos_detalles` (
  `usuario_id` int(11) NOT NULL,
  `objetivos_aprendizaje` text DEFAULT NULL,
  `nivel_actual` varchar(50) DEFAULT 'Otros'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `materia_nombre_manual` varchar(100) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion_minutos` int(11) DEFAULT 60,
  `precio_total` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','aceptada','rechazada','completada') DEFAULT 'pendiente',
  `link_aula` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases`
--

INSERT INTO `clases` (`id`, `profesor_id`, `alumno_id`, `materia_nombre_manual`, `fecha_hora`, `duracion_minutos`, `precio_total`, `estado`, `link_aula`, `fecha_creacion`) VALUES
(1, 1, 2, 'informatica', '2026-05-18 18:00:00', 60, 0.00, 'pendiente', NULL, '2026-05-11 16:43:41'),
(2, 1, 2, 'informatica', '2026-05-18 18:00:00', 60, 0.00, 'pendiente', NULL, '2026-05-11 16:43:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `remitente_id` int(11) NOT NULL,
  `destinatario_id` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `remitente_id`, `destinatario_id`, `contenido`, `leido`, `fecha_envio`) VALUES
(1, 1, 2, '¡Hola silvia! Bienvenido a ISIMatch. Estamos encantados de tenerte aquí. Explora la plataforma y cuéntanos si necesitas ayuda.', 1, '2026-05-08 15:00:28'),
(2, 2, 1, 'lll', 0, '2026-05-11 17:59:14'),
(3, 1, 3, '¡Hola Profe! Bienvenido a ISIMatch. Estamos encantados de tenerte aquí.', 0, '2026-05-11 19:39:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_tarjeta` varchar(20) DEFAULT 'Visa',
  `ultimos_cuatro` varchar(4) NOT NULL,
  `predeterminada` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores_detalles`
--

CREATE TABLE `profesores_detalles` (
  `usuario_id` int(11) NOT NULL,
  `titulo_profesional` varchar(100) DEFAULT 'Profesor en ISIMatch',
  `bio` text DEFAULT NULL,
  `tarifa_hora` decimal(10,2) DEFAULT 15.00,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `valoracion_media` decimal(3,2) DEFAULT 5.00,
  `total_resenas` int(11) DEFAULT 0,
  `idiomas` varchar(255) DEFAULT 'Español',
  `tiempo_respuesta` varchar(100) DEFAULT 'menos de 1h'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores_detalles`
--

INSERT INTO `profesores_detalles` (`usuario_id`, `titulo_profesional`, `bio`, `tarifa_hora`, `linkedin_url`, `valoracion_media`, `total_resenas`, `idiomas`, `tiempo_respuesta`) VALUES
(3, 'informatica', '', 15.00, '', 5.00, 0, 'Español', 'menos de 1h');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `clase_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha_resena` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('alumno','profesor') NOT NULL,
  `anio_nacimiento` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password_hash`, `telefono`, `rol`, `anio_nacimiento`, `fecha_registro`) VALUES
(1, 'Sistema', 'ISIMatch', 'soporte@isimatch.com', '123456', NULL, 'profesor', 2002, '2026-05-08 14:57:59'),
(2, 'silvia', '', 'silvia@hotmail.com', '$2y$10$0ny3fm1LyMXZZX/VxnlUdumDli/ZXRrSIOwf/g7Qtrd9DUASv.yP6', NULL, 'alumno', 2002, '2026-05-08 15:00:28'),
(3, 'Profe', 'Prueba', 'isimatch@hotmail.com', '$2y$10$v8rOXs6eIch8a.D51Em2oujNl3u9QFGX3rJWYHNjM5ko4eiQRb68m', '', 'profesor', 2002, '2026-05-11 19:39:30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos_detalles`
--
ALTER TABLE `alumnos_detalles`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remitente_id` (`remitente_id`),
  ADD KEY `destinatario_id` (`destinatario_id`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `profesores_detalles`
--
ALTER TABLE `profesores_detalles`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clase_id` (`clase_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- NOTA PARA EL DESARROLLADOR:
-- La tabla `resenas` y sus columnas de soporte en `profesores_detalles`
-- ya existen en el esquema original. El sistema de valoraciones usa:
--
--   resenas (clase_id, profesor_id, alumno_id, puntuacion, comentario)
--   profesores_detalles (valoracion_media, total_resenas)
--
-- El controlador PHP/guardar_valoracion.php se encarga de:
--   1. Insertar la nueva reseña en `resenas`.
--   2. Recalcular y actualizar `valoracion_media` y `total_resenas`
--      en `profesores_detalles` automáticamente tras cada valoración.
--
-- Para probar el sistema, cambia el estado de una clase a 'completada':
-- UPDATE clases SET estado = 'completada' WHERE id = 1;
-- --------------------------------------------------------

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos_detalles`
--
ALTER TABLE `alumnos_detalles`
  ADD CONSTRAINT `alumnos_detalles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clases_ibfk_2` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `metodos_pago_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesores_detalles`
--
ALTER TABLE `profesores_detalles`
  ADD CONSTRAINT `profesores_detalles_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resenas_ibfk_3` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
