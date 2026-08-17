<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 📚 Academia Next Level School - Gestor de Horarios

Sistema integral para la generación y gestión de horarios académicos, desarrollado específicamente para el Colegio Academia Next Level School.

## 📋 Acerca del Proyecto

Este sistema permite gestionar eficientemente la asignación de horarios, aulas y profesores, resolviendo el problema de **asignación de restricciones** (evitar cruces de horarios, aulas y profesores) mediante un motor inteligente de generación de horarios.

### 🎯 Módulos Principales
- **Gestión de Disponibilidad:** Panel para que los profesores marquen bloques de horas disponibles
- **Asignación de Cursos y Aulas:** Vinculación de docentes con materias, grupos y salones
- **Motor de Generación de Horarios:** Algoritmo que cruza disponibilidades y genera la malla académica
- **Buscador y Vista Individual:** Búsqueda dinámica por profesor con su ficha semanal
- **Módulo de Exportación:** Excel, PNG y PDF con formato profesional
- **Control de Aulas y Capacidad:** Validación de capacidad física de salones
- **Historial y Control de Cambios:** Sistema de versionado para revertir modificaciones

### 🛠️ Stack Tecnológico
- **Backend:** Laravel 10/11 (PHP 8.1+)
- **Frontend:** Blade + Tailwind CSS + Alpine.js / Vue.js
- **Base de Datos:** MySQL 5.7+ / MariaDB 10.3+
- **Autenticación:** Laravel Sanctum
- **Exportaciones:** PhpSpreadsheet (Excel), DomPDF (PDF), html2canvas (PNG)

---

## 🚀 Configuración Inicial del Proyecto

### 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

```bash
php --version        # PHP 8.1 o superior
composer --version   # Composer 2.x
mysql --version      # MySQL 5.7 o superior / MariaDB 10.3+
node --version       # Node.js 16+ (para assets)
npm --version        # NPM 7+

Instalación
1. Clonar el repositorio
bash
git clone [URL_DEL_REPOSITORIO]
cd academia-horarios
2. Instalar dependencias del backend
bash
composer install
3. Instalar dependencias del frontend
bash
npm install
4. Configurar variables de entorno
bash
cp .env.example .env
# Editar el archivo .env con tus credenciales (ver sección de configuración)
5. Generar clave de la aplicación
bash
php artisan key:generate
6. Configurar la base de datos
sql
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS academia_horarios 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
7. Ejecutar migraciones y seeders
bash
php artisan migrate
php artisan db:seed  # Opcional: datos de prueba

### Estructura de Carpetas del Proyecto
academia-horarios/
├── app/
│   ├── Console/         # Comandos Artisan
│   ├── Enums/           # Enumeraciones (estados, roles, etc.)
│   ├── Exceptions/      # Manejo de excepciones
│   ├── Helpers/         # Funciones auxiliares
│   ├── Http/
│   │   ├── Controllers/ # Controladores
│   │   ├── Middleware/  # Middlewares
│   │   └── Resources/   # API Resources
│   ├── Models/          # Modelos Eloquent
│   ├── Services/        # Lógica de negocio
│   └── Traits/          # Traits reutilizables
├── config/              # Archivos de configuración
├── database/
│   ├── migrations/      # Migraciones de BD
│   └── seeders/         # Datos de prueba
├── public/              # Archivos públicos
├── resources/
│   ├── views/           # Plantillas Blade
│   └── js/              # Assets frontend
├── routes/              # Definición de rutas
├── storage/             # Archivos generados
└── tests/               # Pruebas unitarias

🧪 Comandos Útiles de Desarrollo
bash
# Limpiar caché de la aplicación
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
php artisan optimize

# Ejecutar pruebas
php artisan test

# Crear un nuevo controlador
php artisan make:controller ProfesorController --api

# Crear un nuevo modelo con migración
php artisan make:model Profesor -m

# Crear un nuevo seeder
php artisan make:seeder ProfesorSeeder

# Ejecutar migraciones
php artisan migrate:fresh --seed  # Reinicia la BD con datos de prueba

# Monitorear colas (si se usan)
php artisan queue:work

# Ver rutas disponibles
php artisan route:list
