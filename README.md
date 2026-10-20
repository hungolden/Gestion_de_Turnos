# Sistema de Gestión de Turnos

Aplicación web para gestionar turnos en negocios de estética y servicios (peluquerías, salones de manicura, centros de belleza).

Proyecto de **Práctica Profesional – 2.º Año**, carrera **Analista de Sistemas**, IPET N.º 1308. Ciclo lectivo 2026.

**Autor:** Hun, Leonardo Sebastian

---

## ¿Qué hace?

El sistema tiene tres tipos de usuario:

| Rol | Qué puede hacer |
|---|---|
| **Cliente** | Registrarse, buscar negocios por rubro, ver horarios libres, reservar y cancelar turnos. |
| **Especialista** | Ver su agenda del día, cambiar el estado de sus turnos, cargar turnos manuales y consultar la agenda del local. |
| **Administrador** | Configurar el negocio y gestionar especialidades, especialistas, servicios, usuarios y turnos. Consultar el historial de cambios. |

Antes de confirmar una reserva, el sistema verifica que el especialista no tenga otro turno en ese horario, para evitar superposiciones.

## Tecnologías

- **PHP** (procedural, con `mysqli` y sentencias preparadas)
- **MySQL / MariaDB**, administrado con phpMyAdmin
- **HTML y CSS**
- Entorno local: **XAMPP**

## Cómo instalarlo

1. Instalar [XAMPP](https://www.apachefriends.org/es/) e iniciar **Apache** y **MySQL** desde su panel.
2. Copiar la carpeta `gestion_de_turnos` dentro de `C:\xampp\htdocs\`.
3. Entrar a [http://localhost/phpmyadmin](http://localhost/phpmyadmin), ir a la pestaña **Importar** y elegir el archivo `base_de_datos/gestion_de_turnos.sql`. Ese archivo crea la base y todas las tablas.
4. (Opcional) Importar también `base_de_datos/datos_ejemplo.sql` para tener un negocio de prueba cargado.
5. Abrir [http://localhost/gestion_de_turnos](http://localhost/gestion_de_turnos).

Los datos de conexión a la base están en `conexion.php` (por defecto: usuario `root`, sin contraseña, base `gestion_de_turnos`).

### Usuarios de prueba (con datos de ejemplo)

Todos tienen la contraseña **`demo1234`**.

| Email | Rol |
|---|---|
| admin@bella.com | Administrador |
| martin@bella.com | Especialista |
| carla@mail.com | Cliente |

## Estructura del proyecto

```
gestion_de_turnos/
├── base_de_datos/          Scripts SQL (instalación completa y datos de ejemplo)
├── login/, registro/       Inicio de sesión y registro de cuentas
├── turnos_disponibles/     Búsqueda de negocios y reserva de turnos (cliente)
├── mis_turnos/             Turnos del cliente y cancelación
├── especialista/           Agenda y panel del especialista
├── negocio_config/         Registro y configuración del negocio
├── especialidades/         ABM de especialidades
├── especialistas/          ABM de especialistas
├── servicios/              ABM de servicios
├── especialista_servicios/ Asignación de servicios a especialistas
├── turnos/                 ABM de turnos (administrador)
├── usuarios/               ABM de usuarios
├── historial_turnos/       Historial de cambios de estado
├── notificaciones/         Invitaciones para sumarse al staff
├── conexion.php            Conexión a la base de datos
├── auth_check.php          Control de sesión
└── estilos.css             Estilos generales
```

## Base de datos

La base tiene 9 tablas: `negocio_config`, `usuarios`, `especialidades`, `especialistas`, `servicios`, `especialista_servicios`, `turnos`, `historial_turnos` y `notificaciones`. El diagrama entidad-relación completo está en la carpeta del proyecto.
