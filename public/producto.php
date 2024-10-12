<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/tienda_functions.php';
require_once __DIR__ . '/../includes/utils.php';

$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$producto = getProductoDetalle($producto_id);

if (!$producto) {
    header("Location: " . url('/tienda.php'));
    exit();
}

$companyInfo = getCompanyInfo();
$pageTitle = htmlspecialchars($producto['name']) . " - " . ($companyInfo['name'] ?? 'Nuestra Tienda');

$shareUrl = urlencode(url("/producto.php?id={$producto_id}"));
$shareText = urlencode("¡Mira este producto: {$producto['name']}!");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.css">
    <link rel="stylesheet" href="<?php echo url('/assets/css/tienda.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/assets/css/producto.css'); ?>">
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <header>
                <a href="<?php echo url('/tienda.php'); ?>" class="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver a la tienda
                </a>
                <a href="<?php echo url('/carrito.php'); ?>" class="boton-carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="numerito" class="numerito">0</span>
                </a>
            </header>

            <main class="producto-detalle">
                <div class="producto-imagen">
                    <img src="<?php echo $producto['image_path']; ?>" alt="<?php echo htmlspecialchars($producto['name']); ?>">
                </div>
                <div class="producto-info">
                    <h1><?php echo htmlspecialchars($producto['name']); ?></h1>
                    <p class="precio">$<?php echo $producto['price_formatted']; ?></p>
                    <p class="descripcion"><?php echo nl2br(htmlspecialchars($producto['description'])); ?></p>
                    <button class="agregar-al-carrito" id="agregar-al-carrito" data-id="<?php echo $producto['id']; ?>">
                        Agregar al carrito
                    </button>
<div class="compartir">
    <p>Compartir:</p>
    <a href="https://api.whatsapp.com/send?text=<?php echo $shareText . ' ' . $shareUrl; ?>" target="_blank" class="btn-compartir whatsapp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" class="btn-compartir facebook">
        <i class="fab fa-facebook-f"></i>
    </a>
    <button id="copiar-enlace" class="btn-compartir copiar" data-url="<?php echo url("/producto.php?id={$producto_id}"); ?>">
        <i class="fas fa-link"></i>
    </button>
</div>
                </div>
            </main>
<br>
<br>
<br>
            <footer>
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?>. Todos los derechos reservados.</p>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.js"></script>
    <script>
        var baseUrl = <?php echo json_encode(url('/')); ?>;
    </script>
    <script src="<?php echo url('js/producto.js'); ?>"></script>
</body>
</html>