-- GymFit Smart: estructura completa de la base de datos (sin datos).
-- Uso: phpMyAdmin > Importar. Crea la base "gymfit_smart" automáticamente.
-- Importar en una base nueva: si ya existe con tablas, eliminarla antes.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `gymfit_smart` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `gymfit_smart`;

-- Tabla: datos_alumno
CREATE TABLE `datos_alumno` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `peso` decimal(4,1) NOT NULL,
  `altura` smallint(5) UNSIGNED NOT NULL,
  `edad` tinyint(3) UNSIGNED NOT NULL,
  `sexo` enum('masculino','femenino','otro') NOT NULL,
  `objetivo` varchar(50) NOT NULL,
  `experiencia` varchar(20) DEFAULT NULL,
  `dias_entrenamiento` tinyint(3) UNSIGNED DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(15) NOT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Un registro por alumno con sus datos físicos y objetivos';

-- Tabla: ejercicios
CREATE TABLE `ejercicios` (
  `id` int(11) NOT NULL,
  `rutina_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `series` int(11) NOT NULL,
  `repeticiones` int(11) NOT NULL,
  `peso_sugerido` decimal(10,0) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cada ejercicio pertenece a una rutina';

-- Tabla: progreso
CREATE TABLE `progreso` (
  `id` int(11) NOT NULL,
  `ejercicio_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `peso_utilizado` decimal(6,1) NOT NULL,
  `repeticiones_realizadas` int(11) NOT NULL,
  `registrado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Registro histórico de los ejercicios del alumno';

-- Tabla: rutinas
CREATE TABLE `rutinas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `dia` enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo') DEFAULT NULL,
  `alumno_id` int(11) NOT NULL,
  `entrenador_id` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cada entrenador crea rutinas y les asigna a sus alumnos';

-- Tabla: usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` enum('entrenador','alumno') NOT NULL,
  `entrenador_id` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Guarda tanto entrenadores como alumnos por "rol"';

-- Índices
ALTER TABLE `datos_alumno`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alumno_id` (`alumno_id`);

ALTER TABLE `ejercicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rutina_id` (`rutina_id`);

ALTER TABLE `progreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ejercicio_id` (`ejercicio_id`,`alumno_id`),
  ADD KEY `alumno_id` (`alumno_id`);

ALTER TABLE `rutinas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`,`entrenador_id`),
  ADD KEY `entrenador_id` (`entrenador_id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `entrenador_id` (`entrenador_id`),
  ADD KEY `email_2` (`email`);

-- AUTO_INCREMENT
ALTER TABLE `datos_alumno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ejercicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `progreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rutinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Claves foráneas
ALTER TABLE `datos_alumno`
  ADD CONSTRAINT `datos_alumno_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `ejercicios`
  ADD CONSTRAINT `ejercicios_ibfk_1` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `progreso`
  ADD CONSTRAINT `progreso_ibfk_1` FOREIGN KEY (`ejercicio_id`) REFERENCES `ejercicios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `progreso_ibfk_2` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `rutinas`
  ADD CONSTRAINT `rutinas_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rutinas_ibfk_2` FOREIGN KEY (`entrenador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`entrenador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
