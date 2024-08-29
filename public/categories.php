<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/category_functions.php';


if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('categories_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$categoryId = $_GET['id'] ?? null;

// Procesar acciones antes de incluir el header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            if (hasPermission('categories_create')) {
                $result = addCategory($_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('categories.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            } else {
                $_SESSION['flash_message'] = "No tienes permiso para añadir categorías.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('categories.php'));
                exit;
            }
            break;
        case 'edit':
            if (hasPermission('categories_edit')) {
                $result = updateCategory($categoryId, $_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('categories.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            } else {
                $_SESSION['flash_message'] = "No tienes permiso para editar categorías.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('categories.php'));
                exit;
            }
            break;
        case 'delete':
            if (hasPermission('categories_delete')) {
                $result = deleteCategory($categoryId);
                echo json_encode($result);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar categorías.']);
                exit;
            }
            break;
    }
}

$pageTitle = "Gestión de Categorías";
require_once __DIR__ . '/../includes/header.php';

// Renderizar vistas
switch ($action) {
    case 'list':
        $categories = getAllCategories();
        include __DIR__ . '/../views/categories/list.php';
        break;
    case 'add':
        if (hasPermission('categories_create')) {
            include __DIR__ . '/../views/categories/add.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para añadir categorías.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('categories.php'));
            exit;
        }
        break;
    case 'edit':
        if (hasPermission('categories_edit')) {
            $category = getCategoryById($categoryId);
            include __DIR__ . '/../views/categories/edit.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para editar categorías.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('categories.php'));
            exit;
        }
        break;
    default:
        header('Location: ' . url('categories.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>