-- ============================================================
-- Sistema de Gestión de Turnos — instalación completa de la base
-- Incluye la estructura original + migracion.sql + migracion_2.sql + migracion_3.sql
-- Uso: en phpMyAdmin, pestaña Importar, elegir este archivo.
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestion_de_turnos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_de_turnos;

-- ---------- Estructura original ----------
CREATE TABLE negocio_config (id INT AUTO_INCREMENT PRIMARY KEY, nombre_local VARCHAR(150) NOT NULL, rubro VARCHAR(100) NOT NULL, contacto VARCHAR(100) DEFAULT NULL, apertura TIME NOT NULL, cierre TIME NOT NULL, cancelacion_hs INT NOT NULL DEFAULT 2) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE usuarios (id INT AUTO_INCREMENT PRIMARY KEY, negocio_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, password_hash VARCHAR(255) NOT NULL, rol ENUM('admin','especialista') NOT NULL, estado VARCHAR(50) DEFAULT 'activo', UNIQUE KEY idx_email_negocio (email, negocio_id), FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE especialidades (id INT AUTO_INCREMENT PRIMARY KEY, negocio_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE especialistas (id INT AUTO_INCREMENT PRIMARY KEY, negocio_id INT NOT NULL, usuario_id INT DEFAULT NULL, especialidad_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, estado VARCHAR(50) DEFAULT 'activo', FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE, FOREIGN KEY (especialidad_id) REFERENCES especialidades (id) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE servicios (id INT AUTO_INCREMENT PRIMARY KEY, negocio_id INT NOT NULL, nombre VARCHAR(150) NOT NULL, duracion_min INT NOT NULL, precio DECIMAL(10,2) NOT NULL, estado VARCHAR(50) DEFAULT 'activo', FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE especialista_servicios (especialista_id INT NOT NULL, servicio_id INT NOT NULL, PRIMARY KEY (especialista_id, servicio_id), FOREIGN KEY (especialista_id) REFERENCES especialistas (id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (servicio_id) REFERENCES servicios (id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE turnos (id INT AUTO_INCREMENT PRIMARY KEY, negocio_id INT NOT NULL, especialista_id INT NOT NULL, servicio_id INT NOT NULL, cliente_nombre VARCHAR(100) NOT NULL, cliente_contacto VARCHAR(100) DEFAULT NULL, fecha DATE NOT NULL, hora_inicio TIME NOT NULL, hora_fin TIME NOT NULL, estado ENUM('Confirmado','Asistido','Cancelado') DEFAULT 'Confirmado', FOREIGN KEY (negocio_id) REFERENCES negocio_config (id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (especialista_id) REFERENCES especialistas (id) ON UPDATE CASCADE, FOREIGN KEY (servicio_id) REFERENCES servicios (id) ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE historial_turnos (id INT AUTO_INCREMENT PRIMARY KEY, turno_id INT NOT NULL, usuario_id INT DEFAULT NULL, estado_anterior VARCHAR(50) DEFAULT NULL, estado_nuevo VARCHAR(50) NOT NULL, fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (turno_id) REFERENCES turnos (id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- migracion.sql: registro público + un negocio por administrador ----------
ALTER TABLE usuarios MODIFY negocio_id INT NULL;
ALTER TABLE usuarios MODIFY rol ENUM('usuario','especialista','admin') NOT NULL DEFAULT 'usuario';
ALTER TABLE usuarios ADD UNIQUE KEY uk_usuarios_email (email);
ALTER TABLE especialista_servicios ADD UNIQUE KEY uk_esp_serv (especialista_id, servicio_id);

-- ---------- migracion_2.sql: turnos reservados por usuarios clientes ----------
ALTER TABLE turnos ADD COLUMN cliente_usuario_id INT NULL, ADD INDEX idx_turnos_cliente_usuario (cliente_usuario_id);

-- ---------- migracion_3.sql: panel del especialista + notificaciones ----------

ALTER TABLE turnos
  MODIFY estado ENUM('Confirmado','Retrasado','Asistido','Finalizado','Cancelado') DEFAULT 'Confirmado';

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
