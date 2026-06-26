# Prueba Técnica - Guía de Instalación

## Características del proyecto

- Laravel 13
- PHP 8.4
- Autenticación con Sanctum
- Documentación OpenAPI/Swagger
- Endpoints para gestión de usuarios
- Soporte para migraciones y seeders

## Requisitos

- PHP 8.4
- Composer
- MySQL / MariaDB / PostgreSQL (según tu configuración)
- Node.js (opcional para assets, Vite)

## Instalación

1. Clonar el repositorio:

```bash
git clone <URL_DEL_REPOSITORIO> prueba_tecnica
cd prueba_tecnica
```

2. Instalar dependencias de PHP:

```bash
composer install
```

3. Copiar el archivo de entorno y generar la clave de aplicación:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_de_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

5. Instalar dependencias de JavaScript (opcional):

```bash
npm install
```

## Migraciones

Ejecuta las migraciones para crear las tablas necesarias en la base de datos:

```bash
php artisan migrate
```

Si deseas correr las migraciones desde cero y limpiar tablas anteriores:

```bash
php artisan migrate:fresh
```

## Seeders

Para cargar datos iniciales en la base de datos, utiliza los seeders incluidos:

```bash
php artisan db:seed
```

Si usas `migrate:fresh` y también quieres sembrar los datos en una sola operación:

```bash
php artisan migrate:fresh --seed
```

## Ejecución local

Inicia el servidor de desarrollo:

```bash
php artisan serve
```

Accede a la aplicación en:

```text
http://127.0.0.1:8000
```

## Uso del proyecto

- `routes/api.php` contiene las rutas de la API.
- `app/Http/Controllers/UsersController.php` contiene la lógica de los endpoints de usuarios.
- `app/Models/User.php` define el modelo de usuario y su esquema OpenAPI.

## Documentación API

Si la documentación Swagger está configurada, puedes generar los archivos OpenAPI con:

```bash
php artisan l5-swagger:generate
```

Y luego abrir la interfaz Swagger según la configuración del proyecto.

## Notas

- Asegúrate de tener PHP 8.4 instalado.
- Laravel 13 ofrece mejoras de rendimiento y compatibilidad moderna.
- Revisa `.env` para ajustar la configuración de la base de datos y el entorno.

## Licencia

Proyecto con licencia MIT.
