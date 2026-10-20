-- ============================================================
-- Migración 3: pantalla del especialista + invitaciones (notificaciones)
-- Ejecutar UNA sola vez en phpMyAdmin, sobre la base del sistema
-- (además de migracion.sql y migracion_2.sql, que ya deberías haber corrido).
-- ============================================================

-- 1) Nuevos estados de turno: Retrasado y Finalizado.
ALTER TABLE turnos
  MODIFY estado ENUM('Confirmado','Retrasado','Asistido','Finalizado','Cancelado') DEFAULT 'Confirmado';

-- 2) Notificaciones. Hoy se usa para la invitación "el local X te contrata".
--    estado: pendiente -> (aceptada | rechazada)
CREATE TABLE IF NOT EXISTS notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,                 -- a quién le llega
  negocio_id INT NOT NULL,                 -- qué local lo contrata
  especialista_id INT NULL,                -- ficha de especialista creada por el admin
  tipo VARCHAR(30) NOT NULL DEFAULT 'contratacion',
  mensaje VARCHAR(255) NOT NULL,
  estado ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta DATETIME NULL,
  INDEX idx_notif_usuario (usuario_id, estado),
  FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (especialista_id) REFERENCES especialistas (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
