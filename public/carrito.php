<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/utils.php';

$companyInfo = getCompanyInfo();
$pageTitle = "Carrito - " . ($companyInfo['name'] ?? 'Nuestra Tienda');

// Obtener el número de teléfono y formatearlo para WhatsApp
$whatsappNumber = preg_replace('/[^0-9]/', '', $companyInfo['phone']);
if (substr($whatsappNumber, 0, 2) !== '54') {
    $whatsappNumber = '54' . $whatsappNumber;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('/assets/css/tienda.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/assets/css/carrito.css'); ?>">
</head>
<body>
    <div class="wrapper">
        <header>
            <a href="<?php echo url('/tienda.php'); ?>" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver a la tienda
            </a>
            <h1>Carrito de Compras</h1>
            <div id="contador-carrito" class="contador-carrito">0</div>
        </header>

        <main>
            <div id="contenedor-carrito" class="contenedor-carrito">
                <!-- Los productos del carrito se cargarán aquí dinámicamente -->
            </div>
            <div id="acciones-carrito" class="acciones-carrito">
                <div id="total-carrito"></div>
                <button id="vaciar-carrito" class="btn-vaciar" data-tooltip="Eliminar todos los productos del carrito">Vaciar Carrito</button>
                <button id="comprar-carrito" class="btn-comprar" data-tooltip="Proceder al pago">Comprar</button>
            </div>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?>. Todos los derechos reservados.</p>
        </footer>
    </div>

    <!-- Modal para datos del cliente -->
    <div id="modal-cliente" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Datos del Cliente</h2>
            <form id="form-cliente">
                <div class="form-group">
                    <label for="nombre">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección de envío:</label>
                    <textarea id="direccion" name="direccion" required></textarea>
                </div>
                <button type="submit" class="btn-enviar">Enviar Pedido</button>
            </form>
        </div>
    </div>

    <script src="<?php echo url('js/carrito.js'); ?>"></script>
    <script>
        var whatsappNumber = "<?php echo $whatsappNumber; ?>";
    </script>
</body>
</html>