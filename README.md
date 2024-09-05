# Sistema POS (Point of Sale)

## Descripción
Este sistema POS (Point of Sale) es una solución integral para la gestión de ventas, inventario, clientes y proveedores. Diseñado para pequeñas y medianas empresas, ofrece una interfaz intuitiva y funcionalidades robustas para optimizar las operaciones diarias de un negocio.

## Características Principales

- Gestión de usuarios con roles y permisos
- Gestión de productos y categorías
- Proceso de ventas y reservas
- Gestión de clientes y proveedores
- Gestión de inventario
- Reportes básicos de ventas
- Interfaz responsive basada en Bootstrap

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache o Nginx recomendado)
- Extensiones PHP: PDO, PDO_MySQL
- Navegador web moderno (Chrome, Firefox, Safari, Edge)

## Instalación

1. Clone el repositorio en su servidor web:
   ```
   git clone https://github.com/tu-usuario/sistema-pos.git
   ```

2. Configure su servidor web para que apunte al directorio `public` como raíz del documento.

3. Copie el archivo `config/config.example.php` a `config/config.php` y ajuste las configuraciones según su entorno:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nombre_de_su_base_de_datos');
   define('DB_USER', 'su_usuario');
   define('DB_PASS', 'su_contraseña');
   define('BASE_URL', 'http://su-dominio.com');
   ```

4. Importe el esquema de la base de datos ejecutando el script SQL ubicado en `database/schema.sql`.

5. Acceda a la URL de su instalación y siga el asistente de configuración inicial para crear el usuario administrador.

## Uso

1. Acceda al sistema utilizando las credenciales del administrador creadas durante la instalación.

2. Comience configurando las categorías de productos, productos y proveedores.

3. Añada usuarios adicionales y asígneles roles según sea necesario.

4. Utilice el módulo de ventas para procesar transacciones.

5. Explore las diferentes funcionalidades del sistema a través del menú principal.

## Estructura del Proyecto

- `public/`: Punto de entrada de la aplicación y assets públicos
- `includes/`: Funciones y utilidades PHP
- `views/`: Archivos de vistas PHP
- `config/`: Archivos de configuración
- `database/`: Scripts SQL y migraciones
- `js/`: Scripts JavaScript del lado del cliente

## Seguridad

- Asegúrese de mantener el archivo `config.php` fuera del acceso público.
- Utilice siempre HTTPS en producción para proteger la transmisión de datos.
- Actualice regularmente todas las dependencias y el propio sistema.

## Contribución

Si desea contribuir al proyecto, por favor:

1. Haga un fork del repositorio
2. Cree una nueva rama para su característica (`git checkout -b feature/AmazingFeature`)
3. Haga commit de sus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Haga push a la rama (`git push origin feature/AmazingFeature`)
5. Abra un Pull Request

## Soporte

Para reportar problemas o solicitar nuevas características, por favor abra un issue en el repositorio de GitHub.

## Licencia

Este proyecto está licenciado bajo la Licencia MIT. Vea el archivo `LICENSE` para más detalles.