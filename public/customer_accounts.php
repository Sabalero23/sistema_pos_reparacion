<?php
require_once __DIR__ . '/../config/config.php';
global $pdo;
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/roles.php';
require_once ROOT_PATH . '/includes/customer_account_functions.php';

// Manejo de solicitudes AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    if (!isLoggedIn() || !hasPermission('customer_accounts_view')) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para acceder a esta función.']);
        exit;
    }

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_pending_installments':
            ob_start();
            if (!hasPermission('customer_accounts_view')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver las cuotas pendientes.']);
                exit;
            }
            $accountId = $_GET['account_id'] ?? null;
            if (!$accountId) {
                echo json_encode(['success' => false, 'message' => 'ID de cuenta no proporcionado.']);
                exit;
            }
            $result = getPendingInstallments($accountId);
            $output = ob_get_clean();
            if (!empty($output)) {
                error_log("Salida inesperada antes del JSON: " . $output);
            }
            echo json_encode($result);
            exit;
            break;

        case 'add_payment':
            ob_start();
            if (!hasPermission('customer_accounts_adjust')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para registrar pagos.']);
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                global $pdo;
                if (!isset($pdo) || !($pdo instanceof PDO)) {
                    error_log("Error: La conexión a la base de datos no es válida en add_payment");
                    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
                    exit;
                }
                $result = addPayment($_POST);
                $output = ob_get_clean();
                if (!empty($output)) {
                    error_log("Salida inesperada antes del JSON en add_payment: " . $output);
                }
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            }
            exit;
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
            exit;
    }
}

// Manejo de solicitudes normales (no AJAX)
if (!isLoggedIn() || !hasPermission('customer_accounts_view')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$accountId = $_GET['id'] ?? null;
$customerId = $_GET['customer_id'] ?? null;

$pageTitle = "Cuentas de Clientes";
require_once ROOT_PATH . '/includes/header.php';

// Obtener clientes con cuotas vencidas o próximas
$clientsWithIssues = getClientsWithOverdueOrUpcomingInstallments();

?>
<style>
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal {
        z-index: 1050 !important;
    }
    .modal-dialog {
        z-index: 1060 !important;
    }
    .modal-content {
        z-index: 1070 !important;
    }
    body.modal-open {
        overflow: hidden;
    }
</style>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $pageTitle; ?></h1>
        <?php if (hasPermission('customer_accounts_add')): ?>
            <a href="<?php echo url('customer_accounts.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Nueva Cuenta
            </a>
        <?php endif; ?>
    </div>

    <?php
    try {
        switch ($action) {
            case 'list':
                $accounts = getAllCustomerAccounts();
                if ($accounts === false) {
                    setFlashMessage("Error al obtener las cuentas de clientes. Por favor, consulte el registro de errores.", 'error');
                    $accounts = [];
                }
                include ROOT_PATH . '/views/customer_accounts/list.php';
                break;

            case 'view':
                if (!$accountId) {
                    throw new Exception("ID de cuenta no proporcionado.");
                }
                $account = getCustomerAccount($accountId);
                if (!$account) {
                    throw new Exception("Cuenta de cliente no encontrada.");
                }
                include ROOT_PATH . '/views/customer_accounts/view.php';
                break;

            case 'add':
                if (!hasPermission('customer_accounts_add')) {
                    throw new Exception("No tienes permiso para añadir cuentas de clientes.");
                }
                $customers = getAllCustomers();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = addCustomerAccount($_POST);
                    if ($result['success']) {
                        setFlashMessage($result['message'], 'success');
                        redirect('customer_accounts.php');
                    } else {
                        $error = $result['message'];
                    }
                }
                include ROOT_PATH . '/views/customer_accounts/add_edit.php';
                break;

            case 'edit':
                if (!hasPermission('customer_accounts_edit')) {
                    throw new Exception("No tienes permiso para editar cuentas de clientes.");
                }
                if (!$accountId) {
                    throw new Exception("ID de cuenta no proporcionado.");
                }
                $account = getCustomerAccount($accountId);
                if (!$account) {
                    throw new Exception("Cuenta de cliente no encontrada.");
                }
                $customers = getAllCustomers();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = updateCustomerAccount($accountId, $_POST);
                    if ($result['success']) {
                        setFlashMessage($result['message'], 'success');
                        redirect('customer_accounts.php');
                    } else {
                        $error = $result['message'];
                    }
                }
                include ROOT_PATH . '/views/customer_accounts/add_edit.php';
                break;

            case 'add_payment':
                if (!hasPermission('customer_accounts_adjust')) {
                    echo json_encode(['success' => false, 'message' => 'No tienes permiso para registrar pagos.']);
                    exit;
                }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = addPayment($_POST);
                    echo json_encode($result);
                    exit;
                }
                break;
            
            case 'get_pending_installments':
                ob_start();
                header('Content-Type: application/json');
                if (!hasPermission('customer_accounts_view')) {
                    echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver las cuotas pendientes.']);
                    exit;
                }
                $accountId = $_GET['account_id'] ?? null;
                if (!$accountId) {
                    echo json_encode(['success' => false, 'message' => 'ID de cuenta no proporcionado.']);
                    exit;
                }
                $result = getPendingInstallments($accountId);
                $output = ob_get_clean();
                if (!empty($output)) {
                    error_log("Salida inesperada antes del JSON: " . $output);
                }
                echo json_encode($result);
                exit;
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
    ?>
</div>

<!-- Modal para clientes con problemas de pago -->
<div class="modal fade" id="clientIssuesModal" tabindex="-1" aria-labelledby="clientIssuesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientIssuesModalLabel">Clientes con Cuotas Vencidas o Próximas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
var clientsWithIssues = <?php echo json_encode($clientsWithIssues); ?>;
</script>
<script src="<?php echo url('js/customer_accounts_modal.js'); ?>"></script>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>