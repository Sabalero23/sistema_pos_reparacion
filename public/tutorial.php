<?php
// Asegúrate de que este archivo esté incluido en tu sistema de autenticación
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Verifica si el usuario está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Título de la página
$pageTitle = "Tutorial del Sistema POS y Gestión de Taller de Reparaciones";

// Incluye el encabezado de tu sitio
require_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        h2 {
            color: #34495e;
            font-size: 2rem;
            margin-top: 40px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #34495e;
        }

        ol, ul {
            padding-left: 30px;
        }

        li {
            margin-bottom: 10px;
        }

        code {
            background-color: #eee;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: 'Courier New', Courier, monospace;
        }

        p {
            text-align: justify;
        }

        a {
            color: #2980b9;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #1abc9c;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .intro {
            font-size: 1.1rem;
            font-style: italic;
            color: #555;
            margin-bottom: 20px;
        }

        .alert {
            background-color: #f1c40f;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $pageTitle; ?></h1>
        <p class="intro">Este tutorial le guiará a través de todas las funcionalidades del Sistema POS y Gestión de Taller de Reparaciones. Siga los pasos detallados para familiarizarse con cada aspecto del sistema.</p>

        <h2>Índice</h2>
        <ol>
            <li><a href="#introduccion">Introducción</a></li>
            <li><a href="#acceso">Acceso al sistema</a></li>
            <li><a href="#configuracion">Configuración inicial</a></li>
            <li><a href="#inventario">Gestión de inventario</a></li>
            <li><a href="#clientes">Gestión de clientes</a></li>
            <li><a href="#proveedores">Gestión de proveedores</a></li>
            <li><a href="#categorias">Categorías de productos</a></li>
            <li><a href="#pos">Sistema de Punto de Venta (POS)</a></li>
            <li><a href="#ventas">Gestión de ventas</a></li>
            <li><a href="#compras">Gestión de compras</a></li>
            <li><a href="#presupuestos">Gestión de presupuestos</a></li>
            <li><a href="#reparaciones">Órdenes de reparación</a></li>
            <li><a href="#caja">Gestión de caja registradora</a></li>
            <li><a href="#reportes">Sistema de reportes</a></li>
            <li><a href="#roles">Configuración de roles y permisos</a></li>
        </ol>

    <h2 id="introduccion">Introducción</h2>
    <p>Este tutorial le guiará a través de todas las funcionalidades del Sistema POS y Gestión de Taller de Reparaciones. Siga los pasos detallados para familiarizarse con cada aspecto del sistema.</p>

    <h2 id="acceso">Acceso al sistema</h2>
    <ol>
        <li>Abra su navegador web y vaya a la URL del sistema.</li>
        <li>En la página de inicio de sesión, ingrese sus credenciales.</li>
        <li>Haga clic en "Iniciar sesión".</li>
    </ol>

    <h2 id="configuracion">Configuración inicial</h2>
    <ol>
        <li>Vaya a "Configuración" en el menú principal.</li>
        <li>Configure los detalles de su negocio (nombre, dirección, información de contacto).</li>
        <li>Ajuste las preferencias de impuestos y moneda.</li>
        <li>Configure los métodos de pago aceptados.</li>
        <li>Guarde los cambios.</li>
    </ol>

    <h2 id="inventario">Gestión de inventario</h2>
    <ol>
        <li>Acceda a "Inventario" en el menú principal.</li>
        <li>Para añadir un nuevo producto:
            <ul>
                <li>Haga clic en "Añadir producto".</li>
                <li>Complete los detalles del producto (nombre, descripción, precio, cantidad, etc.).</li>
                <li>Asigne una categoría y un proveedor al producto.</li>
                <li>Establezca los niveles de alerta de stock bajo.</li>
                <li>Guarde el nuevo producto.</li>
            </ul>
        </li>
        <li>Para editar un producto existente:
            <ul>
                <li>Busque el producto en la lista.</li>
                <li>Haga clic en el icono de edición.</li>
                <li>Modifique los detalles necesarios y guarde los cambios.</li>
            </ul>
        </li>
        <li>Para ver los movimientos de stock:
            <ul>
                <li>Seleccione un producto.</li>
                <li>Haga clic en "Ver movimientos".</li>
                <li>Revise el historial de entradas y salidas del producto.</li>
            </ul>
        </li>
    </ol>
	
<h2 id="clientes">Gestión de clientes</h2>
    <ol>
        <li>Vaya a "Clientes" en el menú principal.</li>
        <li>Para añadir un nuevo cliente:
            <ul>
                <li>Haga clic en "Añadir cliente".</li>
                <li>Complete los detalles del cliente y guarde.</li>
            </ul>
        </li>
        <li>Para editar un cliente existente:
            <ul>
                <li>Busque el cliente en la lista.</li>
                <li>Haga clic en el icono de edición.</li>
                <li>Modifique los detalles necesarios y guarde los cambios.</li>
            </ul>
        </li>
        <li>Para ver el historial de un cliente:
            <ul>
                <li>Seleccione un cliente.</li>
                <li>Haga clic en "Ver historial".</li>
                <li>Revise las compras, reparaciones y presupuestos asociados al cliente.</li>
            </ul>
        </li>
    </ol>

    <h2 id="proveedores">Gestión de proveedores</h2>
    <ol>
        <li>Acceda a "Proveedores" en el menú principal.</li>
        <li>Para añadir un nuevo proveedor:
            <ul>
                <li>Haga clic en "Añadir proveedor".</li>
                <li>Complete los detalles del proveedor y guarde.</li>
            </ul>
        </li>
        <li>Para editar un proveedor existente:
            <ul>
                <li>Busque el proveedor en la lista.</li>
                <li>Haga clic en el icono de edición.</li>
                <li>Modifique los detalles necesarios y guarde los cambios.</li>
            </ul>
        </li>
        <li>Para ver los productos asociados a un proveedor:
            <ul>
                <li>Seleccione un proveedor.</li>
                <li>Haga clic en "Ver productos".</li>
            </ul>
        </li>
    </ol>

    <h2 id="categorias">Categorías de productos</h2>
    <ol>
        <li>Vaya a "Categorías" en el menú de inventario.</li>
        <li>Para añadir una nueva categoría:
            <ul>
                <li>Haga clic en "Añadir categoría".</li>
                <li>Ingrese el nombre y la descripción de la categoría.</li>
                <li>Guarde la nueva categoría.</li>
            </ul>
        </li>
        <li>Para editar una categoría existente:
            <ul>
                <li>Busque la categoría en la lista.</li>
                <li>Haga clic en el icono de edición.</li>
                <li>Modifique los detalles y guarde los cambios.</li>
            </ul>
        </li>
    </ol>

    <h2 id="pos">Sistema de Punto de Venta (POS)</h2>
    <ol>
        <li>Acceda al POS desde el botón "POS" en la página de ventas.</li>
        <li>Para procesar una venta:
            <ul>
                <li>Busque y seleccione los productos que el cliente desea comprar.</li>
                <li>Ajuste las cantidades si es necesario.</li>
                <li>Aplique descuentos si corresponde.</li>
                <li>Seleccione el método de pago.</li>
                <li>Haga clic en "Finalizar venta".</li>
                <li>Complete la transacción e imprima el recibo si es necesario.</li>
            </ul>
        </li>
    </ol>
	
	<h2 id="ventas">Gestión de ventas</h2>
    <ol>
        <li>Vaya a "Ventas" en el menú principal.</li>
        <li>Para ver el historial de ventas:
            <ul>
                <li>Utilice los filtros para buscar ventas específicas.</li>
                <li>Haga clic en una venta para ver los detalles.</li>
            </ul>
        </li>
        <li>Para anular una venta:
            <ul>
                <li>Seleccione la venta.</li>
                <li>Haga clic en "Anular venta".</li>
                <li>Confirme la anulación.</li>
            </ul>
        </li>
    </ol>

    <h2 id="compras">Gestión de compras</h2>
    <ol>
        <li>Acceda a "Compras" en el menú principal.</li>
        <li>Para registrar una nueva compra:
            <ul>
                <li>Haga clic en "Nueva compra".</li>
                <li>Seleccione el proveedor.</li>
                <li>Añada los productos y cantidades.</li>
                <li>Ingrese el costo total.</li>
                <li>Guarde la compra.</li>
            </ul>
        </li>
        <li>Para ver el historial de compras:
            <ul>
                <li>Utilice los filtros para buscar compras específicas.</li>
                <li>Haga clic en una compra para ver los detalles.</li>
            </ul>
        </li>
    </ol>

    <h2 id="presupuestos">Gestión de presupuestos</h2>
    <ol>
        <li>Vaya a "Presupuestos" en el menú principal.</li>
        <li>Para crear un nuevo presupuesto:
            <ul>
                <li>Haga clic en "Nuevo presupuesto".</li>
                <li>Seleccione el cliente o cree uno nuevo.</li>
                <li>Añada los productos o servicios al presupuesto.</li>
                <li>Establezca los precios y descuentos.</li>
                <li>Guarde el presupuesto.</li>
            </ul>
        </li>
        <li>Para editar un presupuesto:
            <ul>
                <li>Busque el presupuesto en la lista.</li>
                <li>Haga clic en el icono de edición.</li>
                <li>Realice los cambios necesarios y guarde.</li>
            </ul>
        </li>
        <li>Para cambiar el estado de un presupuesto:
            <ul>
                <li>Seleccione el presupuesto.</li>
                <li>Haga clic en "Cambiar estado".</li>
                <li>Elija el nuevo estado (aprobado, rechazado, etc.).</li>
            </ul>
        </li>
        <li>Para descargar un presupuesto en PDF:
            <ul>
                <li>Seleccione el presupuesto.</li>
                <li>Haga clic en "Descargar PDF".</li>
            </ul>
        </li>
        <li>Para acceder a la vista de cliente:
            <ul>
                <li>Copie el enlace de "Vista de cliente".</li>
                <li>Comparta este enlace con el cliente para que pueda ver el presupuesto.</li>
            </ul>
        </li>
    </ol>
	
	<h2 id="reparaciones">Órdenes de reparación</h2>
    <ol>
        <li>Acceda a "Reparaciones" en el menú principal.</li>
        <li>Para crear una nueva orden de reparación:
            <ul>
                <li>Haga clic en "Nueva reparación".</li>
                <li>Seleccione el cliente o cree uno nuevo.</li>
                <li>Describa el problema o servicio requerido.</li>
                <li>Establezca una fecha estimada de finalización.</li>
                <li>Guarde la orden de reparación.</li>
            </ul>
        </li>
        <li>Para actualizar el estado de una reparación:
            <ul>
                <li>Busque la orden en la lista.</li>
                <li>Haga clic en "Actualizar estado".</li>
                <li>Seleccione el nuevo estado y añada notas si es necesario.</li>
            </ul>
        </li>
        <li>Para finalizar una reparación:
            <ul>
                <li>Seleccione la orden.</li>
                <li>Haga clic en "Finalizar reparación".</li>
                <li>Registre los servicios y piezas utilizadas.</li>
                <li>Genere la factura correspondiente.</li>
            </ul>
        </li>
    </ol>

    <h2 id="caja">Gestión de caja registradora</h2>
    <ol>
        <li>Acceda a "Caja registradora" en el menú principal.</li>
        <li>Para abrir caja:
            <ul>
                <li>Haga clic en "Abrir caja".</li>
                <li>Ingrese el saldo inicial.</li>
                <li>Confirme la apertura de caja.</li>
            </ul>
        </li>
        <li>Para registrar movimientos:
            <ul>
                <li>Seleccione "Entrada" o "Salida" de efectivo.</li>
                <li>Ingrese el monto y una descripción.</li>
                <li>Guarde el movimiento.</li>
            </ul>
        </li>
        <li>Para ver el estado actual:
            <ul>
                <li>Revise el resumen que muestra el saldo inicial, movimientos y saldo actual.</li>
            </ul>
        </li>
        <li>Para cerrar caja:
            <ul>
                <li>Haga clic en "Cerrar caja".</li>
                <li>Verifique el saldo final calculado por el sistema.</li>
                <li>Ingrese el saldo final real (efectivo físico).</li>
                <li>Confirme el cierre de caja.</li>
            </ul>
        </li>
    </ol>

    <h2 id="reportes">Sistema de reportes</h2>
    <ol>
        <li>Vaya a "Reportes" en el menú principal.</li>
        <li>Seleccione el tipo de reporte que desea generar:
            <ul>
                <li>Ventas</li>
                <li>Compras</li>
                <li>Inventario</li>
                <li>Clientes</li>
                <li>Servicios</li>
                <li>Movimientos de caja</li>
            </ul>
        </li>
        <li>Configure los parámetros del reporte:
            <ul>
                <li>Rango de fechas</li>
                <li>Categorías específicas (si aplica)</li>
                <li>Otros filtros relevantes</li>
            </ul>
        </li>
        <li>Haga clic en "Generar reporte".</li>
        <li>Revise el reporte generado que incluirá:
            <ul>
                <li>Resúmenes y totales</li>
                <li>Gráficos y visualizaciones (si aplica)</li>
                <li>Detalles específicos según el tipo de reporte</li>
            </ul>
        </li>
        <li>Opción para exportar el reporte en diferentes formatos (PDF, Excel, etc.)</li>
    </ol>

    <h2 id="roles">Configuración de roles y permisos</h2>
        <ol>
            <li>Acceda a "Configuración" y seleccione "Roles y permisos".</li>
            <li>Para crear un nuevo rol:
                <ul>
                    <li>Haga clic en "Nuevo rol".</li>
                    <li>Asigne un nombre y descripción al rol.</li>
                    <li>Seleccione los permisos correspondientes.</li>
                    <li>Guarde el nuevo rol.</li>
                </ul>
            </li>
            <li>Para editar un rol existente:
                <ul>
                    <li>Seleccione el rol de la lista.</li>
                    <li>Modifique los permisos según sea necesario.</li>
                    <li>Guarde los cambios.</li>
                </ul>
            </li>
            <li>Para asignar un rol a un usuario:
                <ul>
                    <li>Vaya a la sección de usuarios.</li>
                    <li>Seleccione el usuario.</li>
                    <li>Asigne el rol deseado.</li>
                    <li>Guarde los cambios.</li>
                </ul>
            </li>
        </ol>

        <p class="alert">Este tutorial cubre las principales funcionalidades del sistema. Para obtener ayuda adicional o información sobre características específicas, consulte la documentación completa o póngase en contacto con el soporte técnico.</p>
    </div>

    <?php
    // Incluye el pie de página de tu sitio
    require_once __DIR__ . '/../includes/footer.php';
    ?>
</body>
</html>