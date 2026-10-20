-- Datos de ejemplo (opcional). Importar DESPUÉS de gestion_de_turnos.sql.
-- Todos los usuarios tienen la contraseña: demo1234
--   admin@bella.com  -> administrador del negocio
--   martin@bella.com -> especialista
--   carla@mail.com   -> cliente
USE gestion_de_turnos;
SET NAMES utf8mb4;
INSERT INTO negocio_config VALUES (1,'Estudio Bella','Peluquería y estética','351 555-1234','09:00','19:00',2);
INSERT INTO usuarios (id,negocio_id,nombre,email,password_hash,rol,estado) VALUES
(1,1,'Laura Gómez','admin@bella.com','$2y$10$DwOGgXhcR/4Zwtk6vbrNXeVclyVRkmyJruNN4xB8c.Fk12bkwWvaK','admin','activo'),
(2,1,'Martín Díaz','martin@bella.com','$2y$10$DwOGgXhcR/4Zwtk6vbrNXeVclyVRkmyJruNN4xB8c.Fk12bkwWvaK','especialista','activo'),
(3,1,'Sofía Ruiz','sofia@bella.com','$2y$10$DwOGgXhcR/4Zwtk6vbrNXeVclyVRkmyJruNN4xB8c.Fk12bkwWvaK','especialista','activo'),
(4,NULL,'Carla Pérez','carla@mail.com','$2y$10$DwOGgXhcR/4Zwtk6vbrNXeVclyVRkmyJruNN4xB8c.Fk12bkwWvaK','usuario','activo');
INSERT INTO especialidades VALUES (1,1,'Peluquería'),(2,1,'Manicuría'),(3,1,'Cosmetología');
INSERT INTO especialistas VALUES (1,1,2,1,'Martín Díaz','activo'),(2,1,3,2,'Sofía Ruiz','activo');
INSERT INTO servicios VALUES (1,1,'Corte de pelo',30,8000,'activo'),(2,1,'Coloración',90,25000,'activo'),(3,1,'Manicura semipermanente',60,12000,'activo'),(4,1,'Limpieza facial',45,15000,'activo');
INSERT INTO especialista_servicios VALUES (1,1),(1,2),(2,3),(2,4);
INSERT INTO turnos (negocio_id,especialista_id,servicio_id,cliente_nombre,cliente_contacto,cliente_usuario_id,fecha,hora_inicio,hora_fin,estado) VALUES
(1,1,1,'Carla Pérez','carla@mail.com',4,'2026-10-22','10:00','10:30','Confirmado'),
(1,1,2,'Juan López','351 444-1111',NULL,'2026-10-22','11:00','12:30','Confirmado'),
(1,2,3,'Ana Torres','351 444-2222',NULL,'2026-10-22','09:30','10:30','Asistido'),
(1,2,4,'Carla Pérez','carla@mail.com',4,'2026-10-23','15:00','15:45','Confirmado');
INSERT INTO historial_turnos (turno_id,usuario_id,estado_anterior,estado_nuevo) VALUES (3,3,'Confirmado','Asistido');
