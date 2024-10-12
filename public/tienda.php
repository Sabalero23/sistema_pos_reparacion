<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/tienda_functions.php';
require_once __DIR__ . '/../includes/utils.php';

$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$productos_por_pagina = ITEMS_PER_PAGE;

$total_productos = contarProductos($categoria_id);
$total_paginas = max(1, ceil($total_productos / $productos_por_pagina));

$pagina_actual = min($pagina_actual, $total_paginas);
$productos = getProductos($categoria_id, $pagina_actual, $productos_por_pagina);

$companyInfo = getCompanyInfo();
$pageTitle = "Tienda - " . ($companyInfo['name'] ?? 'Nuestra Tienda');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.css">
    <link rel="stylesheet" href="<?php echo url('/assets/css/tienda.css'); ?>">
</head>
<body>
    <div class="wrapper">
        <?php include 'tienda_sidebar_menu.php'; ?>

        <div class="main-content">
            <header>
                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <form id="formulario-busqueda" class="search-bar" action="<?php echo url('/busqueda.php'); ?>" method="get">
                    <input type="text" name="q" id="termino-busqueda" placeholder="Buscar productos..." required>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <a href="<?php echo url('/carrito.php'); ?>" class="boton-carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="numerito" class="numerito">0</span>
                </a>
            </header>

            <main>
                <h1 id="titulo-principal" class="titulo-principal">
                    <?php echo $categoria_id ? htmlspecialchars($productos[0]['category_name'] ?? '') : 'Todos los productos'; ?>
                </h1>
                
                <div id="contenedor-productos" class="contenedor-productos">
                    <?php foreach ($productos as $producto): ?>
                        <div class="producto">
                            <img src="<?php echo $producto['image_path']; ?>" alt="<?php echo htmlspecialchars($producto['name']); ?>">
                            <div class="producto-detalles">
                                <h3><?php echo htmlspecialchars($producto['name']); ?></h3>
                                <p class="precio">$<?php echo $producto['price_formatted']; ?></p>
                                <a href="<?php echo url("/producto.php?id={$producto['id']}"); ?>" class="ver-producto">Ver producto</a>
                                <button class="producto-agregar" id="producto-<?php echo $producto['id']; ?>" data-id="<?php echo $producto['id']; ?>">Agregar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php 
                if ($total_paginas > 1) {
                    $url_base = $categoria_id 
                        ? url("/tienda.php?categoria={$categoria_id}")
                        : url("/tienda.php");
                    echo generarPaginacion($pagina_actual, $total_paginas, $url_base); 
                }
                ?>
            </main>
<br>
<br>
<br>
            <div class="footer-container">
                <footer>
                    <p class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?>. Todos los derechos reservados.</p>
                </footer>
                <a href="<?php echo url('/index.php'); ?>" class="pos-link">Sistema POS</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.js"></script>
    <script>
        window.baseUrl = <?php echo json_encode(BASE_URL); ?>;
        window.initialProducts = <?php echo json_encode($productos); ?>;
    </script>
    <script src="<?php echo url('js/tienda.js'); ?>"></script>
</body>
</html>