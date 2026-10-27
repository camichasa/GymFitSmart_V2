-- Tabla para guardar los datos físicos, objetivos y contacto de emergencia
-- que el alumno completa en "Mis datos" (alumno/mis_datos.php).
-- Ejecutar una sola vez en phpMyAdmin, dentro de la base gymfit_smart.
-- Es segura sobre tu base actual: no modifica ni borra ninguna tabla existente.

CREATE TABLE IF NOT EXISTS `datos_alumno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_id` (`alumno_id`),
  CONSTRAINT `datos_alumno_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Un registro por alumno con sus datos físicos y objetivos';
