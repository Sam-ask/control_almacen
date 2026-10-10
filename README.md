# WMS Almacén - Sistema de Gestión de Inventario

Un Sistema de Gestión de Almacenes web desarrollado con arquitectura MVC para controlar el flujo de mercancía, administrar Órdenes de Compra (POs) y auditar los movimientos de inventario en planta.

## Características Principales

- Gestión de Órdenes de Compra: Creación, edición y seguimiento de POs con soporte para subida de archivos físicos (PDF/Excel).
- Recepción de Mercancía (Kardex): Actualización dinámica de stock mediante transacciones seguras en la base de datos para garantizar la integridad de la información.
- Gestión de Aclaraciones: Sistema automatizado de notificaciones por correo electrónico a proveedores en caso de discrepancias, utilizando SMTP.
- Reportes y Exportación: Generación en tiempo real de reportes de inventario en formatos PDF y Excel.
- Interfaz Dinámica y Responsiva: Filtros multicriterio y acciones asíncronas (AJAX/Fetch API) mediante ventanas modales sin recargar la página.

## Tecnologías Utilizadas

Frontend:
- HTML5 & CSS3
- JavaScript (ES6+, Fetch API)
- Bootstrap 5 (UI/UX)
- Bootstrap Icons

Backend:
- PHP (POO, PDO)
- Arquitectura MVC (Model-View-Controller)

Base de Datos:
- MySQL (Consultas preparadas para evitar Inyección SQL)

Librerías de Terceros (vía Composer):
- FPDF: Para la generación de reportes en PDF.
- PHPMailer: Para el envío seguro de correos transaccionales.

## Instalación y Configuración Local

Sigue estos pasos para ejecutar el proyecto en tu entorno local (ej. XAMPP, Laragon).

### 1. Clonar el repositorio

git clone https://github.com/Sam-ask/control-almacen.git
cd control-almacen

### 2. Configurar la Base de Datos

- Crea una base de datos en MySQL llamada control-almacen-wms.
- Importa el archivo SQL proporcionado (si cuentas con uno en la carpeta del proyecto) o ejecuta tus scripts de creación de tablas.
- Ve a la carpeta config/.
- Duplica el archivo database.example.php y renómbralo a database.php.
- Abre database.php y coloca tus credenciales locales:

private $host = "localhost"; 
private $db-name = "control-almacen-wms"; 
private $username = "root"; 
private $password = "tu-contraseña"; 

### 3. Instalar Dependencias

Asegúrate de tener Composer instalado en tu equipo. Abre la terminal en la raíz del proyecto y ejecuta:

composer install

### 4. Configurar Servidor de Correos (Opcional)

Para que las notificaciones de aclaraciones funcionen, configura tus credenciales SMTP en el archivo correspondiente, asegurándote de usar una Contraseña de Aplicación si usas Gmail.

### 5. Estructura de Carpetas (Uploads)

El sistema requiere una carpeta para guardar los PDFs subidos. Asegúrate de que exista la ruta uploads/purchase-orders/ y que tenga permisos de escritura.

## Estructura del Proyecto

control-almacen/
- assets/             # Archivos CSS, JS e imágenes
- config/             # Conexión a BD y seguridad (database.php ignorado en Git)
- controllers/        # Lógica de negocio (OrderController, MailController, etc.)
- models/             # Consultas a BD (OrdenCompra, Producto, etc.)
- views/              # Interfaces de usuario (Dashboard, Inventario, Órdenes)
- uploads/            # Archivos subidos por usuarios (ignorado en Git)
- vendor/             # Dependencias de Composer (ignorado en Git)
- composer.json       # Configuración de paquetes
- README.md           # Documentación del proyecto

## Seguridad Implementada

- Protección de Credenciales: El archivo de conexión a la base de datos principal está excluido mediante el archivo gitignore.
- Inyección SQL: Todo el sistema utiliza PDO y Prepared Statements.
- Validación de Archivos: Verificación estricta de MIME types y límite de peso en el backend para la subida de archivos.
