Sistema POS y Gestión de Órdenes de Trabajo
Descripción General
Este sistema es una solución integral para Punto de Venta (POS) y Gestión de Órdenes de Trabajo, diseñado para pequeñas y medianas empresas. Ofrece funcionalidades para manejar ventas, inventario, servicios de reparación, gestión de clientes, y mucho más.
Características Principales


Descripción Detallada

1. Ventas y Compras

Ventas: Gestión de transacciones de venta, incluyendo creación, visualización, edición y cancelación.
Presupuestos: Creación y gestión de presupuestos para clientes.
Compras: Administración de compras a proveedores, incluyendo recepción de mercancía.
Reservas: Sistema para manejar reservas de productos.
Promociones: Creación y aplicación de promociones y descuentos.

2. Inventario

Productos: Gestión del catálogo de productos.
Categorías: Organización de productos en categorías.
Proveedores: Administración de información de proveedores.
Control de Inventario: Seguimiento de stock, ajustes y movimientos.

3. Caja

Gestión de Caja: Apertura, cierre y gestión de sesiones de caja.
Movimientos de Caja: Registro de entradas y salidas de efectivo.

4. Clientes

Gestión de Clientes: Mantenimiento de la base de datos de clientes.
Cuentas de Clientes: Seguimiento de saldos y transacciones por cliente.

5. Reparaciones y Servicios

Órdenes de Servicio: Gestión de servicios de reparación.
Visitas a Domicilio: Programación y seguimiento de visitas técnicas.
Calendario: Vista general de servicios y visitas programadas.

6. Reportes

Generación de informes diversos sobre ventas, inventario, etc.

7. Usuarios y Roles

Gestión de Usuarios: Administración de cuentas de usuario del sistema.
Roles y Permisos: Asignación de roles y permisos específicos.

8. Configuración

Configuración General: Ajustes generales del sistema.
Datos de la Empresa: Gestión de la información de la empresa.
Copias de Seguridad: Creación y restauración de backups.

Desglose de Permisos por Categoría
Usuarios

Ver, crear, editar y eliminar usuarios

Productos

Ver, crear, editar y eliminar productos

Categorías

Ver, crear, editar y eliminar categorías

Ventas

Ver, crear, editar y cancelar ventas

Presupuestos

Ver, crear, editar, eliminar y cambiar estado de presupuestos

Caja Registradora

Gestionar, abrir, cerrar y registrar movimientos de caja

Proveedores

Ver, crear, editar y eliminar proveedores

Clientes

Ver, crear, editar y eliminar clientes

Cuentas de Clientes

Ver y ajustar cuentas de clientes

Pagos

Ver, crear, editar y eliminar pagos

Órdenes de Servicio

Ver, crear, editar, eliminar y actualizar estado de órdenes de servicio

Visitas a Domicilio

Ver, crear, editar y eliminar visitas a domicilio
Ver calendario

Reportes

Ver y generar reportes

Roles y Permisos

Gestionar roles y permisos

Reservas

Ver, crear, editar, eliminar, confirmar, cancelar y convertir reservas

Promociones

Ver, crear, editar, eliminar y aplicar promociones

Inventario

Ver, actualizar, ajustar, ver movimientos y ver stock bajo

Compras

Ver, crear, editar, eliminar, recibir y ver movimientos de compras

Configuración del Sistema

Ver y editar configuración, ver configuración de la empresa

Auditoría

Ver registros de auditoría

Backup y Restauración

Crear, restaurar, eliminar y descargar copias de seguridad


Requisitos

PHP 7.4 o superior
MySQL 5.7 o superior
Servidor web (Apache, Nginx, etc.)
Extensiones de PHP: PDO, PDO_MySQL

Instalación

Clone el repositorio o descargue los archivos en su servidor web.
Cree una base de datos MySQL para el sistema.
Copie el archivo config/config.example.php a config/config.php y edite las siguientes constantes:

DB_HOST: El host de su base de datos (generalmente 'localhost')
DB_NAME: El nombre de la base de datos que creó
DB_USER: El usuario de la base de datos
DB_PASS: La contraseña del usuario de la base de datos
BASE_URL: La URL base de su instalación (ej. 'https://sudominio.com')


Acceda a https://sudominio.com/install.php para ejecutar el instalador.
Siga las instrucciones en pantalla para completar la instalación.
Una vez finalizada la instalación, elimine el archivo install.php por seguridad.

Estructura de la base de datos
El sistema utiliza las siguientes tablas principales:

users: Almacena la información de los usuarios del sistema.
products: Contiene el catálogo de productos.
categories: Categorías de productos.
sales: Registra las ventas realizadas.
sale_items: Detalles de los productos incluidos en cada venta.
customers: Información de los clientes.
suppliers: Datos de los proveedores.
inventory: Control de stock de productos.
settings: Configuraciones generales del sistema.

Uso
Después de la instalación, puede acceder al sistema utilizando las siguientes credenciales por defecto:

Email: admin@admin.com
Contraseña: admin1234

Importante: Cambie la contraseña inmediatamente después de su primer inicio de sesión.
Soporte
Si encuentra algún problema o tiene alguna pregunta, por favor abra un issue en el repositorio del proyecto o contacte al equipo de soporte en info@cellcomweb.com.ar.
Licencia
Este proyecto está licenciado bajo la Licencia MIT. Consulte el archivo LICENSE para más detalles.# sistema_pos_reparacion
