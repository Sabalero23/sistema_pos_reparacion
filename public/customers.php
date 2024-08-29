<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/customer_functions.php';

// Habilitar el registro de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

// Verificar permisos
if (!hasPermission('customers_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$customerId = $_GET['id'] ?? null;

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            if (hasPermission('customers_create')) {
                $result = addCustomer($_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('customers.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            } else {
                $error = "No tienes permiso para añadir clientes.";
            }
            break;
        case 'edit':
            if (hasPermission('customers_edit')) {
                if ($customerId) {
                    $result = updateCustomer($customerId, $_POST);
                    if ($result['success']) {
                        $_SESSION['flash_message'] = $result['message'];
                        $_SESSION['flash_type'] = 'success';
                        header('Location: ' . url('customers.php'));
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = "ID de cliente no proporcionado para edición.";
                }
            } else {
                $error = "No tienes permiso para editar clientes.";
            }
            break;
        case 'delete':
            if (hasPermission('customers_delete')) {
                if ($customerId) {
                    $result = deleteCustomer($customerId);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                    exit;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'ID de cliente no proporcionado']);
                    exit;
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar clientes']);
                exit;
            }
            break;
    }
}

$pageTitle = "Gestión de Clientes";
require_once __DIR__ . '/../includes/header.php';

// Manejar diferentes acciones
switch ($action) {
    case 'list':
        $customers = getAllCustomers();
        include __DIR__ . '/../views/customers/list.php';
        break;
    case 'add':
        if (hasPermission('customers_create')) {
            include __DIR__ . '/../views/customers/add.php';
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para añadir clientes.</div>";
        }
        break;
    case 'edit':
        if (hasPermission('customers_edit')) {
            if (!$customerId) {
                $_SESSION['flash_message'] = "ID de cliente no proporcionado para edición.";
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . url('customers.php'));
                exit;
            }
            $customer = getCustomerById($customerId);
            if ($customer) {
                include __DIR__ . '/../views/customers/edit.php';
            } else {
                echo "<div class='alert alert-danger'>Cliente no encontrado.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para editar clientes.</div>";
        }
        break;
    case 'view_account':
        if (!$customerId) {
            $_SESSION['flash_message'] = "ID de cliente no proporcionado para ver la cuenta.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('customers.php'));
            exit;
        }
        $customer = getCustomerById($customerId);
        if ($customer) {
            $accountSummary = getCustomerAccountSummary($customerId);
            $transactions = getCustomerTransactions($customerId);
            include __DIR__ . '/../views/customers/view_account.php';
        } else {
            echo "<div class='alert alert-danger'>Cliente no encontrado.</div>";
        }
        break;
    default:
        $_SESSION['flash_message'] = "Acción no válida.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('customers.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>