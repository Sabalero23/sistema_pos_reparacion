<?php
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/roles.php';
require_once ROOT_PATH . '/includes/customer_account_functions.php';

if (!isLoggedIn() || !hasPermission('customer_accounts_view')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$customerId = $_GET['id'] ?? null;

// Solo incluir el header si no es una acción AJAX
if ($action !== 'add_payment') {
    $pageTitle = "Cuentas de Clientes";
    require_once ROOT_PATH . '/includes/header.php';
}

try {
    switch ($action) {
        case 'list':
            $accounts = getAllCustomerAccounts();
            include ROOT_PATH . '/views/customer_accounts/list.php';
            break;

        case 'view':
            if (!$customerId) {
                throw new Exception("ID de cliente no proporcionado.");
            }
            $account = getCustomerAccount($customerId);
            if (!$account) {
                throw new Exception("Cuenta de cliente no encontrada.");
            }
            if ($account['name'] === 'Consumidor Final' || $account['total_sales'] == 0) {
                throw new Exception("No se puede ver esta cuenta de cliente.");
            }
            $sales = getCustomerSales($customerId);
            $payments = getPayments($customerId);
            include ROOT_PATH . '/views/customer_accounts/view.php';
            break;

        case 'add_payment':
            header('Content-Type: application/json');
            if (!hasPermission('customer_accounts_adjust')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para registrar pagos.']);
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = addPayment($_POST);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            }
            exit;

        default:
            redirect('customer_accounts.php');
            exit;
    }
} catch (Exception $e) {
    if ($action === 'add_payment') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } else {
        setFlashMessage($e->getMessage(), 'error');
        redirect('customer_accounts.php');
    }
    exit;
}

// Solo incluir el footer si no es una acción AJAX
if ($action !== 'add_payment') {
    require_once ROOT_PATH . '/includes/footer.php';
}
?>