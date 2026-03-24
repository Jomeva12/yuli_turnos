# Guía Maestra de Despliegue: yuli_turnos en Hostinger (SSH)

Esta guía está diseñada para que una IA asistente pueda guiar al usuario en el despliegue del sistema de turnos. Sigue estos pasos meticulosamente.

## 1. Preparación Local
Antes de subir nada, asegúrate de que el proyecto está listo para producción:
- Generar assets de Vite: `npm run build`
- Limpiar caché local: `php artisan config:clear`
- Crear el paquete: `yuli_deploy.zip` excluyendo `vendor` y `node_modules`.
  > [!TIP]
  > Si el archivo ZIP es muy grande y falla la subida, crea una carpeta temporal local con los archivos necesarios y súbelos via FTP o arrastrándolos al Administrador de Archivos de Hostinger.

## 2. Requisitos del Servidor (Hostinger)
- **PHP**: Versión 8.2 o superior.
- **SSH**: Habilitado (Puerto 65002 generalmente).
- **Subdominio**: Asegúrate de que apunte a `public_html/yuli/` (o la carpeta raíz del proyecto).

## 3. Configuración de Base de Datos
Pide al usuario que cree una base de datos MySQL en Hostinger.
> [!IMPORTANT]
> Solicita al usuario los siguientes datos: **Nombre de BD**, **Usuario** y **Contraseña**. 
> Si es posible, pide una **captura de pantalla** de la configuración de la base de datos para verificar el nombre del host (suele ser `127.0.0.1` o `localhost`).

## 4. Configuración del Entorno (.env)
Al subir los archivos, el archivo `.env` local no debe subirse. En el servidor:
1. Copiar el ejemplo: `cp .env.example .env`
2. **CRITICAL**: Cambiar de SQLite a MySQL.
   ```bash
   sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
   sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g' .env
   ```
3. Editar el archivo `.env` manualmente (`nano .env`) y poner las credenciales que el usuario proporcionó.

## 5. Instalación de Dependencias (Interacción del Usuario)
Debido a límites de memoria y tiempo en SSH, la IA debe pedir al usuario que ejecute manualmente el comando de Composer:

**Instrucción para el usuario:**
"Por favor, corre este comando en tu terminal SSH dentro de la carpeta del proyecto:"
```bash
composer install --no-dev --optimize-autoloader
```

## 6. Finalización del Despliegue
Una vez instalado Composer, la IA puede ejecutar (o pedir al usuario ejecutar) los pasos finales:
```bash
php artisan key:generate
php artisan migrate:fresh --seed --force
php artisan storage:link
```

## 7. Optimización de URL (Quitar /public)
Si el subdominio no permite cambiar el DocumentRoot a `/public`, crea un archivo `.htaccess` en la raíz del proyecto (`yuli/`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 8. Verificación Final
Acceder a `https://yuli.diferencialdx.com` y verificar que:
- Los empleados se listan correctamente (prueba de conexión a BD).
- Los iconos y estilos cargan (prueba de assets y storage link).