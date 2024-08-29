<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/inventory_functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('inventory_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'adjust') {
    if (hasPermission('inventory_adjust')) {
        $adjustmentData = [
            'user_id' => $_SESSION['user_id'],
            'notes' => $_POST['notes'] ?? ''
        ];

        $items = [];
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $productId => $data) {
                $items[] = [
                    'product_id' => $productId,
                    'new_quantity' => $data['new_quantity'] ?? 0,
                    'reason' => $data['reason'] ?? ''
                ];
            }
        }

        $result = createInventoryAdjustment($adjustmentData, $items);
        if ($result['success']) {
            $_SESSION['flash_message'] = "Ajuste de inventario realizado con éxito.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Error al realizar el ajuste de inventario: " . $result['message'];
            $_SESSION['flash_type'] = 'error';
        }
        header('Location: ' . url('inventory.php'));
        exit;
    }
}

$pageTitle = "Gestión de Inventario";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        $products = getAllProducts();
        include __DIR__ . '/../views/inventory/list.php';
        break;
    case 'adjust':
        if (!hasPermission('inventory_adjust')) {
            $_SESSION['flash_message'] = "No tienes permiso para ajustar el inventario.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('inventory.php'));
            exit;
        }
        $products = getAllProducts();
        include __DIR__ . '/../views/inventory/adjust.php';
        break;
    case 'movements':
        if (!hasPermission('inventory_view_movements')) {
            $_SESSION['flash_message'] = "No tienes permiso para ver los movimientos de inventario.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('inventory.php'));
            exit;
        }
        $productId = $_GET['product_id'] ?? null;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $movements = getStockMovements($productId, $startDate, $endDate);
        include __DIR__ . '/../views/inventory/movements.php';
        break;
    case 'low_stock':
        if (!hasPermission('inventory_view_low_stock')) {
            $_SESSION['flash_message'] = "No tienes permiso para ver los productos con bajo stock.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('inventory.php'));
            exit;
        }
        $lowStockProducts = getLowStockProducts();
        include __DIR__ . '/../views/inventory/low_stock.php';
        break;
    default:
        header('Location: ' . url('inventory.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>