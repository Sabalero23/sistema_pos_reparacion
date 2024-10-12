<?php
// perfil.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/utils.php';
require_once __DIR__ . '/../includes/tienda_functions.php';

$companyInfo = getCompanyInfo();
$pageTitle = "Perfil - " . ($companyInfo['name'] ?? 'Nuestra Tienda');

// Simular un usuario autenticado
$usuario_id = 1; // Reemplazar por el ID del usuario actual

$ordenes = getOrdenes($usuario_id);

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
                <h1 id="titulo-principal" class="titulo-principal">Mi Perfil</h1>

                <div class="ordenes-usuario">
                    <h2>Mis Órdenes</h2>
                    <?php if (empty($ordenes)): ?>
                        <p>No tienes órdenes registradas.</p>
                    <?php else: ?>
                        <table class="tabla-ordenes">
                            <thead>
                                <tr>
                                    <th>Orden #</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordenes as $orden): ?>
                                    <tr>
                                        <td><?php echo $orden['id']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($orden['created_at'])); ?></td>
                                        <td>$<?php echo number_format($orden['total'], 2, ',', '.'); ?></td>
                                        <td>
                                            <a href="<?php echo url('/orden.php?id=' . $orden['id']); ?>" class="boton-ver">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
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