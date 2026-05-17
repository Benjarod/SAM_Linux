# Sistema de Autogestión de Marketing (SAM)
## SAM que está corriendo en el servidor
SAM es un sistema integral para la gestión y automatización de envíos de correos electrónicos masivos, desarrollado como proyecto profesional por **Esteban Andrés Rojas Barra** durante su práctica 2025. Diseñado para instituciones y empresas que necesitan comunicarse efectivamente con sus clientes o miembros, SAM permite a cada usuario:

- 📁 Importar registros de contactos desde archivos Excel.
- 🧩 Filtrar y seleccionar destinatarios para campañas.
- 🎨 Crear y administrar plantillas promocionales personalizadas (texto e imágenes).
- 📬 Enviar correos masivos usando sus propias credenciales SMTP (Mailtrap, Mailgun, Gmail, etc.).
- 🔒 Visualizar únicamente sus datos gracias a una arquitectura multiusuario segura.

## 🌟 Características Principales

- **Gestión de Destinatarios:** Importación y administración de contactos con validación automática.
- **Plantillas HTML:** Editor TinyMCE auto-hospedado, sin marcas comerciales.
- **Subida de Imágenes:** Desde el dispositivo, con vista previa en tiempo real.
- **SMTP Personalizado:** Cada usuario configura su propio servidor (Gmail, Mailtrap, etc.).
- **Adaptación Dinámica:** El formulario de gestión de correos se ajusta según el servidor SMTP.
- **Seguridad y Autenticación:** Laravel Sanctum, API protegida por tokens, datos aislados por usuario.
- **Preparado para Producción:** Código limpio, modular, y listo para despliegue (AWS u otros).

## 🧱 Tecnologías Utilizadas

### Backend
- Laravel 10 (PHP)
- MySQL
- Sanctum (Auth)
- API RESTful

### Frontend
- Angular 16
- Bootstrap 4
- SweetAlert2
- TinyMCE (local)
- FileSaver, XLSX

## 📁 Estructura del Proyecto

sam-proyecto-final/
├── backend-laravel/ # API REST con Laravel 10 + Sanctum
├── frontend-angular/ # SPA desarrollada con Angular 16

bash
Copiar
Editar

## ⚙️ Requisitos

### Backend
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Laravel CLI
- Cuenta SMTP (Mailtrap, Mailgun, Gmail, etc.)

### Frontend
- Node.js v18.x o superior
- Angular CLI
- TinyMCE en carpeta local (`src/assets/tinymce`)

## 🚀 Instrucciones de Instalación

### Backend

```bash
git clone https://github.com/usuario/sam-proyecto-final.git
cd sam-proyecto-final/backend-laravel

composer install
cp .env.example .env
php artisan key:generate

# Configura tus credenciales en el archivo .env
php artisan migrate
php artisan storage:link
php artisan serve
Asegúrate de crear una base de datos llamada sam y definir correctamente el acceso en .env.

Accede en: http://localhost:8000

Frontend
bash
Copiar
Editar
cd ../frontend-angular
npm install
npm install @tinymce/tinymce-angular
npm install bootstrap jquery sweetalert2 file-saver xlsx --legacy-peer-deps
ng serve
Accede en: http://localhost:4200

TinyMCE se encuentra en src/assets/tinymce, auto-hospedado.

🗄️ Base de Datos
Crear una base de datos MySQL llamada sam.

Ejecutar php artisan migrate para generar las tablas.

📬 Configuración SMTP por Usuario
Cada usuario puede guardar su propia configuración SMTP (mailtrap, mailgun, gmail...).

💡 Uso del Sistema
1. Configuración SMTP
Accede al módulo Configuración SMTP.

Completa los campos según el proveedor:

Para Gmail: el campo “Correo Remitente” se oculta automáticamente.

Para otros (Mailtrap, Mailgun): se requiere “Correo Remitente”.

Guarda la configuración.

2. Gestión de Contactos
Sube un archivo Excel o agrega contactos manualmente.

El sistema valida y permite seleccionar los destinatarios deseados.

Guarda la selección si deseas usarla en campañas.

3. Creación de Plantillas
Usa TinyMCE para crear plantillas HTML.

Soporte para imágenes locales.

Uso de variables dinámicas como {nombre}.

4. Envío de Correos
Selecciona una plantilla.

Ingresa asunto y remitente (si aplica).

Elige destinatarios.

Previsualiza el contenido y envíalo.

🚧 Problemas Comunes y Soluciones
❌ No se pudo guardar configuración SMTP:

Verifica que todos los campos estén completos.

Si usas Gmail, el “username” debe ser un correo válido y no se requiere el campo “remitente”.

📨 Correos no llegan:

Verifica configuración SMTP.

Revisa la carpeta de SPAM.

Verifica que el destinatario tenga correo válido.

✅ Mejoras Finales Aplicadas
Subida de imágenes desde TinyMCE con vista previa.

TinyMCE sin marcas ni advertencias.

Compatibilidad con Gmail, Mailtrap y Mailgun.

Corrección de errores de CORS y permisos.

Diseño modular y navegación fluida entre módulos.

Separación de datos por usuario (100% privado).

🔒 Seguridad y Autenticación
Autenticación vía tokens (Laravel Sanctum).

Acceso privado y seguro a cada módulo.

Acceso multiusuario garantizado sin colisiones.

🔄 Decisiones Técnicas Importantes
❌ Eliminación de Laravel Queues para el envío de correos
Inicialmente, se consideró implementar el envío de correos mediante Laravel Queues para mejorar el rendimiento y permitir el envío asincrónico. Sin embargo, se identificó un problema crítico relacionado con la configuración dinámica del servidor SMTP por parte de los usuarios.

🔍 Problema detectado:
Al cambiar la configuración SMTP (por ejemplo, de Mailtrap a Gmail), los correos enviados mediante el sistema de colas (php artisan queue:work) seguían utilizando la configuración anterior, ya que el worker no recargaba automáticamente los nuevos parámetros del archivo .env o de la base de datos.

Esto provocaba:

Envíos fallidos o con remitentes incorrectos.

Necesidad de reiniciar manualmente el proceso del worker (Ctrl + C y volver a ejecutar) para que tomara la nueva configuración SMTP.

🚫 ¿Por qué se descartó?
Aunque técnicamente funcional, esta solución no era viable para usuarios finales no técnicos, ya que:

Requiere acceso a la terminal del servidor.

Implica conocimientos específicos sobre Laravel Queues.

Genera confusión y riesgo de mal uso del sistema.

✅ Solución adoptada:
Se decidió eliminar el uso de queues para el envío de correos y optar por una ejecución directa desde el controlador, lo que garantiza que siempre se utilice la configuración SMTP más reciente definida por el usuario, sin necesidad de reiniciar procesos.

Esta decisión favorece la usabilidad, confiabilidad y autonomía del usuario final, asegurando una experiencia más estable.
