<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/customer_functions.php'; // Añade esta línea
require_once __DIR__ . '/../includes/customer_account_functions.php';
require_once __DIR__ . '/../includes/payment_functions.php';

// Habilitar el registro de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si es una solicitud AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verificar autenticación
if (!isLoggedIn()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
        exit;
    } else {
        $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . url('login.php'));
        exit;
    }
}

// Verificar permisos
if (!hasPermission('customer_accounts_view')) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para acceder a esta función.']);
        exit;
    } else {
        $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . url('index.php'));
        exit;
    }
}

$action = $_GET['action'] ?? 'list';
$accountId = $_GET['id'] ?? null;
$customerId = $_GET['customer_id'] ?? null;

// Funciones auxiliares para estados
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'al_dia':
            return 'success';
        case 'atrasada':
            return 'danger';
        case 'finalizada':
            return 'secondary';
        default:
            return 'primary';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'al_dia':
            return 'Al día';
        case 'atrasada':
            return 'Atrasada';
        case 'finalizada':
            return 'Finalizada';
        default:
            return ucfirst($status);
    }
}

// Manejar solicitudes AJAX
if ($isAjax) {
    header('Content-Type: application/json');

    switch ($action) {
        case 'process_payment':
            if (!hasPermission('payments_create')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para procesar pagos.']);
                exit;
            }

            $installmentId = $_POST['installmentId'] ?? null;
            $amount = $_POST['paymentAmount'] ?? null;
            $paymentDate = $_POST['paymentDate'] ?? null;
            $paymentMethod = $_POST['paymentMethod'] ?? null;
            $notes = $_POST['paymentNotes'] ?? null;

            if (!$installmentId || !$amount || !$paymentDate || !$paymentMethod) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
                exit;
            }

            $result = processPayment($installmentId, $amount, $paymentDate, $paymentMethod, $notes);
            echo json_encode($result);
            exit;

        case 'get_pending_installments':
            if (!hasPermission('customer_accounts_view')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver las cuotas pendientes.']);
                exit;
            }
            $accountId = $_GET['account_id'] ?? null;
            if (!$accountId) {
                echo json_encode(['success' => false, 'message' => 'ID de cuenta no proporcionado.']);
                exit;
            }
            $pendingInstallments = getPendingInstallments($accountId);
            echo json_encode(['success' => true, 'installments' => $pendingInstallments]);
            exit;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            exit;
    }
}

// Procesar formularios para solicitudes no AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            if (hasPermission('customer_accounts_add')) {
                $result = addCustomerAccount($_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('customer_accounts.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            } else {
                $error = "No tienes permiso para añadir cuentas de clientes.";
            }
            break;

        case 'edit':
            if (hasPermission('customer_accounts_edit')) {
                if ($accountId) {
                    $result = updateCustomerAccount($accountId, $_POST);
                    if ($result['success']) {
                        $_SESSION['flash_message'] = $result['message'];
                        $_SESSION['flash_type'] = 'success';
                        header('Location: ' . url('customer_accounts.php'));
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = "ID de cuenta no proporcionado para edición.";
                }
            } else {
                $error = "No tienes permiso para editar cuentas de clientes.";
            }
            break;
    }
}

$pageTitle = "Gestión de Cuentas de Clientes";
require_once __DIR__ . '/../includes/header.php';

// Manejar diferentes acciones
switch ($action) {
    case 'list':
        $accounts = getAllCustomerAccounts();
        ?>
        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><?php echo $pageTitle; ?></h1>
                <?php if (hasPermission('customer_accounts_add')): ?>
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Cuenta
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($accounts)): ?>
                <div class="alert alert-info">No hay cuentas de clientes registradas.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Monto Total</th>
                                <th>Balance</th>
                                <th>Estado</th>
                                <th>Cuotas Pendientes</th>
                                <th>Próximo Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($account['customer_name']); ?></td>
                                    <td>$<?php echo number_format($account['total_amount'], 2); ?></td>
                                    <td>$<?php echo number_format($account['balance'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusBadgeClass($account['status']); ?>">
                                            <?php echo getStatusLabel($account['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $account['pending_installments']; ?></td>
                                    <td><?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if (hasPermission('customer_accounts_view')): ?>
                                                <a href="?action=view&id=<?php echo $account['id']; ?>" class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (hasPermission('customer_accounts_edit')): ?>
                                                <a href="?action=edit&id=<?php echo $account['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($account['customer_phone'])): ?>
                                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $account['customer_phone']); ?>?text=<?php 
                                                    echo urlencode(
                                                        "Hola " . $account['customer_name'] . ", " .
                                                        "le recordamos que tiene un pago pendiente por $" . number_format($account['balance'], 2) . ". " .
                                                        "Su próximo vencimiento es el " . ($account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A') . ". " .
                                                        "Por favor, contáctenos para realizar el pago."
                                                    );
                                                ?>" 
                                                class="btn btn-sm btn-success" 
                                                target="_blank" 
                                                title="Enviar WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        break;

    case 'view':
        if (!$accountId) {
            $_SESSION['flash_message'] = "ID de cuenta no proporcionado para ver detalles.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('customer_accounts.php'));
            exit;
        }
        $account = getCustomerAccount($accountId);
        if ($account) {
            $installments = getAccountInstallments($accountId);
            $payments = getAccountPayments($accountId);
            include __DIR__ . '/../views/customer_accounts/view.php';
        } else {
            echo "<div class='alert alert-danger'>Cuenta de cliente no encontrada.</div>";
        }
        break;

    case 'add':
        if (hasPermission('customer_accounts_add')) {
            $customers = getAllCustomers();
            include __DIR__ . '/../views/customer_accounts/add_edit.php';
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para añadir cuentas de clientes.</div>";
        }
        break;

    case 'edit':
        if (hasPermission('customer_accounts_edit')) {
            if (!$accountId) {
                $_SESSION['flash_message'] = "ID de cuenta no proporcionado para edición.";
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . url('customer_accounts.php'));
                exit;
            }
            $account = getCustomerAccount($accountId);
            $customers = getAllCustomers();
            if ($account) {
                include __DIR__ . '/../views/customer_accounts/add_edit.php';
            } else {
                echo "<div class='alert alert-danger'>Cuenta de cliente no encontrada.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para editar cuentas de clientes.</div>";
        }
        break;

    case 'print_receipt':
        if (hasPermission('payments_view')) {
            $paymentId = $_GET['payment_id'] ?? null;
            if (!$paymentId) {
                $_SESSION['flash_message'] = "ID de pago no proporcionado.";
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . url('customer_accounts.php'));
                exit;
            }
            $paymentDetails = getPaymentDetails($paymentId);
            if ($paymentDetails) {
                include __DIR__ . '/../views/customer_accounts/payment_receipt.php';
            } else {
                echo "<div class='alert alert-danger'>Detalles del pago no encontrados.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No tienes permiso para ver recibos de pago.</div>";
        }
        break;

    default:
        $_SESSION['flash_message'] = "Acción no válida.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('customer_accounts.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>
    <style>
        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            h1 {
                font-size: 1.5rem;
            }
            .btn {
                padding: .375rem .75rem;
                font-size: .875rem;
            }
            .table {
                font-size: .875rem;
            }
        }
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