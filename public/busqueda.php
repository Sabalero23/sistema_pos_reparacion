<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/tienda_functions.php';
require_once __DIR__ . '/../includes/utils.php';

$companyInfo = getCompanyInfo();
$pageTitle = "Búsqueda - " . ($companyInfo['name'] ?? 'Nuestra Tienda');

$termino_busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$productos_por_pagina = ITEMS_PER_PAGE;

$total_productos = contarProductosBusqueda($termino_busqueda);
$total_paginas = max(1, ceil($total_productos / $productos_por_pagina));
$pagina_actual = min($pagina_actual, $total_paginas);

$productos = buscarProductos($termino_busqueda, $pagina_actual, $productos_por_pagina);

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
    <link rel="stylesheet" href="<?php echo url('/assets/css/busqueda.css'); ?>">
</head>
<body>
    <div class="wrapper">
        <header>
            <a href="<?php echo url('/tienda.php'); ?>" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver a la tienda
            </a>
            <h1>Resultados para: "<?php echo htmlspecialchars($termino_busqueda); ?>"</h1>
            <a href="<?php echo url('/carrito.php'); ?>" class="boton-carrito">
                <i class="fas fa-shopping-cart"></i>
                <span id="numerito" class="numerito">0</span>
            </a>
        </header>

        <main>
            <div class="debug-info">
                <p>Término de búsqueda: <?php echo htmlspecialchars($termino_busqueda); ?></p>
                <p>Total de productos encontrados: <?php echo $total_productos; ?></p>
                <p>Página actual: <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></p>
            </div>

            <div id="contenedor-productos" class="contenedor-productos">
                <!-- Los productos se cargarán aquí dinámicamente -->
            </div>

            <?php 
            if ($total_paginas > 1) {
                $url_base = url('/busqueda.php?q=' . urlencode($termino_busqueda));
                echo generarPaginacion($pagina_actual, $total_paginas, $url_base); 
            }
            ?>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?>. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script>
        var productosEncontrados = <?php echo json_encode($productos); ?>;
        var baseUrl = '<?php echo url('/'); ?>';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.js"></script>
    <script src="<?php echo url('js/busqueda.js'); ?>"></script>
</body>
</html>