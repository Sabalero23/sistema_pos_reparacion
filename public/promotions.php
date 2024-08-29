<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/product_functions.php';
require_once __DIR__ . '/../includes/promotion_functions.php';


if (!isLoggedIn() || !hasPermission('promotions_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$promotionId = $_GET['id'] ?? null;

$pageTitle = "Gestión de Promociones";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        $promotions = getAllPromotions();
        include __DIR__ . '/../views/promotions/list.php';
        break;
    case 'create':
        if (hasPermission('promotions_create')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = createPromotion($_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('promotions.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            $products = getAllProducts();
            include __DIR__ . '/../views/promotions/create.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para crear promociones.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('promotions.php'));
            exit;
        }
        break;
    case 'edit':
        if (!$promotionId) {
            header('Location: ' . url('promotions.php'));
            exit;
        }
        if (hasPermission('promotions_edit')) {
            $promotion = getPromotionById($promotionId);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = updatePromotion($promotionId, $_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('promotions.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            $products = getAllProducts();
            include __DIR__ . '/../views/promotions/edit.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para editar promociones.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('promotions.php'));
            exit;
        }
        break;
    case 'delete':
        if (!$promotionId) {
            header('Location: ' . url('promotions.php'));
            exit;
        }
        if (hasPermission('promotions_delete')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = deletePromotion($promotionId);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'danger';
                }
                header('Location: ' . url('promotions.php'));
                exit;
            }
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para eliminar promociones.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('promotions.php'));
            exit;
        }
        break;
    default:
        header('Location: ' . url('promotions.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>