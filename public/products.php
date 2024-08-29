<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/product_functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('products_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$productId = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            $result = addProduct($_POST);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('products.php'));
                exit;
            } else {
                $error = $result['message'];
            }
            break;
        case 'edit':
            if ($productId) {
                $result = updateProduct($productId, $_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('products.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            break;
    }
}

$pageTitle = "Gestión de Productos";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'delete':
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = deleteProduct($productId);
            echo json_encode($result);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        break;
    case 'list':
        $products = getAllProducts();
        $categories = getAllCategories();
        include __DIR__ . '/../views/products/list.php';
        break;
    case 'add':
        $categories = getAllCategories();
        $suppliers = getAllSuppliers();
        include __DIR__ . '/../views/products/add.php';
        break;
    case 'edit':
        if (!$productId) {
            header('Location: ' . url('products.php'));
            exit;
        }
        $product = getProductById($productId);
        $categories = getAllCategories();
        $suppliers = getAllSuppliers();
        include __DIR__ . '/../views/products/edit.php';
        break;
    default:
        header('Location: ' . url('products.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>