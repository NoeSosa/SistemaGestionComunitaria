# Sistema de Gestión Comunitaria

Sistema integral para la gestión de procesos comunitarios, desarrollado con Laravel y Filament.

## Descripción

Este proyecto es una plataforma administrativa diseñada para facilitar la gestión de información y procesos dentro de una comunidad o municipio. Permite el control de ciudadanos, asambleas, multas y otros aspectos relevantes de la administración comunitaria.

## Características Principales

- **Panel Administrativo Robusto**: Construido sobre [Filament PHP](https://filamentphp.com/), ofreciendo una interfaz moderna y responsiva.
- **Gestión de Roles y Permisos**: Implementado con [Filament Shield](https://github.com/bezhanSalleh/filament-shield) para un control de acceso granular.
- **Registro de Actividades**: Trazabilidad completa de las acciones de los usuarios mediante [Spatie Activitylog](https://spatie.be/docs/laravel-activitylog).
- **Copias de Seguridad**: Sistema automatizado de backups utilizando [Spatie Laravel Backup](https://spatie.be/docs/laravel-backup).
- **Generación de Códigos QR**: Funcionalidad integrada para generar códigos QR para diversos usos.

## Requisitos del Sistema

- PHP ^8.2
- Composer
- Node.js & NPM
- Base de datos compatible con Laravel (MySQL, PostgreSQL, SQLite, etc.)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/NoeSosa/SistemaGestionComunitaria.git
   cd SistemaGestionComunitaria
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Frontend**
   ```bash
   npm install
   npm run build
   ```

4. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar base de datos**
   - Crea una base de datos vacía.
   - Actualiza las credenciales de base de datos en el archivo `.env`.

6. **Ejecutar migraciones**
   ```bash
   php artisan migrate --seed
   ```

7. **Crear usuario administrativo**
   ```bash
   php artisan shield:super-admin
   ```

## Uso

Para iniciar el servidor de desarrollo:

```bash
php artisan serve
```

Accede al panel administrativo en `http://localhost:8000/admin`.

## Créditos

- Desarrollado por Noe Sosa.
- Basado en [Laravel](https://laravel.com).
