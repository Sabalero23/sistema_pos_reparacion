<?php
// orden.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../includes/tienda_functions.php';

$companyInfo = getCompanyInfo();
$pageTitle = "Detalles de la Orden - " . ($companyInfo['name'] ?? 'Nuestra Tienda');

$orden_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($orden_id === null) {
    redirect('/perfil.php');
}

$detallesOrden = getDetallesOrden($orden_id);
$orden = [
    'id' => $orden_id,
    'total' => 0,
    'created_at' => null,
    'nombre' => null,
    'email' => null,
    'direccion' => null
];

foreach ($detallesOrden as $item) {
    $orden['total'] += $item['price'] * $item['quantity'];
    $orden['created_at'] = $item['created_at'];
    $orden['nombre'] = $item['nombre'];
    $orden['email'] = $item['email'];
    $orden['direccion'] = $item['direccion'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.css">
    <link rel="stylesheet" href="<?php echo url('/assets/css/tienda.css'); ?>">
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="logo">
                <a href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?></a>
            </div>
            <ul>
                <li><a href="<?php echo url('/tienda.php'); ?>" data-categoria-id="">Todos los productos</a></li>
                <?php foreach (getCategorias() as $categoria): ?>
                    <li><a href="#" data-categoria-id="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

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
                <h1 id="titulo-principal" class="titulo-principal">Detalles de la Orden #<?php echo $orden['id']; ?></h1>

                <div class="orden-info">
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($orden['created_at'])); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($orden['nombre']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($orden['email']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($orden['direccion']); ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($orden['total'], 2, ',', '.'); ?></p>
                </div>

                <table class="tabla-orden">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detallesOrden as $item): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?php echo url($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php else: ?>
                                        <img src="/api/placeholder/50/50" alt="Imagen por defecto">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>

            <footer>
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?>. Todos los derechos reservados.</p>
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/toastify.min.js"></script>
    <script src="<?php echo url('/js/tienda.js'); ?>"></script>
</body>
</html>