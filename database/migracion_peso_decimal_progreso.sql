-- MIGRACIÓN (recomendada): permitir decimales en el peso del progreso.
-- Antes: decimal(10,0) guardaba 22.5 kg como 23 kg.
-- Ahora: decimal(6,1) guarda 22.5 kg tal cual.
--
-- Ejecutar una sola vez en phpMyAdmin, dentro de la base gymfit_smart.
-- Los registros que ya existan se conservan.

ALTER TABLE `progreso`
  MODIFY `peso_utilizado` decimal(6,1) NOT NULL;
