-- MIGRACIÓN: el día de entrenamiento pasa a la RUTINA (ya no a cada ejercicio).
-- Ejemplo: la rutina "Pecho" del alumno X se asigna al lunes;
-- sus ejercicios no eligen día, siguen a la rutina.
--
-- Ejecutar una sola vez en phpMyAdmin, dentro de la base gymfit_smart
-- (pestaña SQL o Importar). Se puede repetir sin problema.
-- Está pensado para MariaDB (el que trae XAMPP).
--
-- ATENCIÓN: el paso 2 BORRA la columna "dia" de la tabla ejercicios y los
-- valores que tuviera guardados. Las rutinas existentes quedan "Sin asignar"
-- hasta que el entrenador las edite y elija un día.

-- 1) Nueva columna: día de la rutina
ALTER TABLE `rutinas`
  ADD COLUMN IF NOT EXISTS `dia`
    enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo')
    DEFAULT NULL AFTER `descripcion`;

-- 2) Los ejercicios ya no tienen día propio
ALTER TABLE `ejercicios`
  DROP COLUMN IF EXISTS `dia`;
