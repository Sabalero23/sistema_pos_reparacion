<?php
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/roles.php';
require_once ROOT_PATH . '/includes/customer_account_functions.php';


if (!isLoggedIn() || !hasPermission('payments_create')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$customerId = $_GET['customer_id'] ?? null;

$pageTitle = "Gestión de Pagos";
require_once ROOT_PATH . '/includes/header.php';

try {
    switch ($action) {
        case 'add':
            if (!$customerId) {
                throw new Exception("ID de cliente no proporcionado.");
            }
            $account = getCustomerAccount($customerId);
            if (!$account) {
                throw new Exception("Cuenta de cliente no encontrada.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = addPayment($_POST);
                if ($result['success']) {
                    setFlashMessage($result['message'], 'success');
                    redirect('customer_accounts.php?action=view&id=' . $customerId);
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            include ROOT_PATH . '/views/payments/add.php';
            break;

        default:
            redirect('customer_accounts.php');
            exit;
    }
} catch (Exception $e) {
    setFlashMessage($e->getMessage(), 'error');
    redirect('customer_accounts.php');
    exit;
}

require_once ROOT_PATH . '/includes/footer.php';
?>