<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/supplier_functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('suppliers_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$supplierId = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            $result = addSupplier($_POST);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('suppliers.php'));
                exit;
            } else {
                $error = $result['message'];
            }
            break;
        case 'edit':
            if ($supplierId) {
                $result = updateSupplier($supplierId, $_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('suppliers.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            break;
    }
}

$pageTitle = "Gestión de Proveedores";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'delete':
        if (!$supplierId) {
            echo json_encode(['success' => false, 'message' => 'ID de proveedor no proporcionado']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = deleteSupplier($supplierId);
            echo json_encode($result);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        break;
    case 'list':
        $suppliers = getAllSuppliers();
        include __DIR__ . '/../views/suppliers/list.php';
        break;
    case 'add':
        include __DIR__ . '/../views/suppliers/add.php';
        break;
    case 'edit':
        if (!$supplierId) {
            header('Location: ' . url('suppliers.php'));
            exit;
        }
        $supplier = getSupplierById($supplierId);
        include __DIR__ . '/../views/suppliers/edit.php';
        break;
    default:
        header('Location: ' . url('suppliers.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>