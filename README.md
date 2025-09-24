# EventPass

Hola! Este es mi proyecto **EventPass**, un sistema completo de gestión de eventos y tickets que desarrollé usando Laravel. 

## ¿Qué es EventPass?

EventPass es mi solución para gestionar eventos de manera profesional. Permite crear eventos, vender tickets, y tener un panel de administración completo para controlar todo el proceso.

## Lo que implementé

### Sistema de Login
- Usé **Laravel Breeze** para el login (como en los tutoriales)
- Los usuarios pueden registrarse y hacer login
- Los administradores tienen acceso especial

### Panel de Administración
- Dashboard con estadísticas en tiempo real
- Puedo crear, editar y eliminar eventos
- Puedo gestionar usuarios (crear, editar, eliminar)
- Todo está protegido con middleware

### Características Técnicas
- **Getters y Setters explícitos** en todos los modelos (como me pidieron)
- **Form Requests** para validar datos
- **Logs** de todas las acciones importantes
- **Transacciones** para mantener la integridad de la BD
- **Soft Deletes** para no perder datos

## Cómo ejecutarlo

1. **Clona el proyecto:**
```bash
git clone https://github.com/Camiloesoto/EventPass.git
cd EventPass
```

2. **Instala las dependencias:**
```bash
composer install
```

3. **Configura el .env:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Ejecuta las migraciones:**
```bash
php artisan migrate
```

5. **Crea los usuarios de prueba:**
```bash
php artisan db:seed --class=AdminUserSeeder
```

6. **Inicia el servidor:**
```bash
php artisan serve
```

## Usuarios de Prueba

**Administrador:**
- Email: admin@eventpass.com
- Password: admin123

**Usuario Normal:**
- Email: user@eventpass.com  
- Password: user123

## Estructura que creé

```
EventPass/
├── app/
│   ├── Http/Controllers/Admin/     # Controladores del admin
│   ├── Http/Middleware/          # Middleware de seguridad
│   ├── Models/                   # Modelos con getters/setters
│   └── Enums/                    # Estados y tipos
├── resources/views/
│   ├── layouts/admin.blade.php   # Layout del admin
│   └── admin/                    # Vistas del panel admin
├── routes/
│   ├── web.php                   # Rutas principales
│   └── admin.php                 # Rutas del admin
└── database/
    ├── migrations/               # Migraciones
    └── seeders/                 # Datos de prueba
```

## Funcionalidades que implementé

### 1. Sistema de Login Completo
- Registro y login de usuarios
- Recuperación de contraseña
- Protección de rutas

### 2. Panel de Administración
- Dashboard con estadísticas
- CRUD completo de eventos
- CRUD completo de usuarios
- Sistema de logs

### 3. Seguridad
- Middleware para proteger rutas admin
- Validación de datos con Form Requests
- Logs de todas las acciones

### 4. Modelos con Getters/Setters
- Implementé getters y setters explícitos en todos los modelos
- Siguiendo el diagrama UML que me dieron

## Buenas Prácticas que seguí

- **MVC** - Separé bien las responsabilidades
- **Form Requests** - Validación separada
- **Middleware** - Protección de rutas
- **Logging** - Registro de acciones
- **Transacciones** - Manejo seguro de BD
- **Soft Deletes** - No elimino datos realmente
- **Enums** - Tipos seguros
- **Rutas RESTful** - Organización profesional

## Secciones Principales

1. **Página Principal** (`/`) - Lista de eventos públicos
2. **Panel Admin** (`/admin`) - Dashboard y gestión
3. **Login** (`/login`) - Autenticación

## Tecnologías que usé

- **Laravel 12** - Framework principal
- **PHP 8.2+** - Lenguaje
- **Bootstrap 5** - Estilos
- **Font Awesome** - Iconos
- **Chart.js** - Gráficos
- **SQLite** - Base de datos

## Notas del Desarrollo

Este proyecto lo desarrollé siguiendo todos los tutoriales de Laravel que vimos en clase. Implementé:

- Sistema de login con Laravel Breeze
- CRUD completo con Form Requests
- Middleware para seguridad
- Logs para auditoría
- Getters/setters explícitos según el UML
- Vistas responsivas con Bootstrap

Todo está documentado y sigue las mejores prácticas de Laravel.

## Sobre el Proyecto

Este es mi proyecto final del curso de Laravel. Implementé todo lo que aprendimos en los tutoriales y agregué funcionalidades adicionales como el sistema de logs y el panel de administración completo.

---

**Desarrollado por Camilo Soto**
