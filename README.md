# CRUD simple en PHP (LAMP)

Proyecto CRUD básico en un solo directorio usando:
- Linux
- Apache
- MySQL
- PHP
- `mysqli` orientado a objetos

## Base de datos

Ejecuta `schema.sql` en MySQL para crear:
- Base de datos `tienda`
- Tabla `productos` con campos:
  - `id`
  - `nombre`
  - `detalle`
  - `agregado` (TIMESTAMP automático)

## Configuración rápida

1. Copia esta carpeta dentro de tu servidor Apache (por ejemplo `/var/www/html/pos`).
2. Ajusta las credenciales de MySQL en `db.php`.
3. Abre en tu navegador: `http://localhost/pos/index.php`
