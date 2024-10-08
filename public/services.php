<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/service_functions.php';
require_once __DIR__ . '/../includes/cash_register_functions.php';

if (!isLoggedIn() || !hasPermission('services_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$orderId = $_GET['id'] ?? null;

$pageTitle = "Gestión de Servicios";

try {
    switch ($action) {
        case 'list':
            $orders = getAllServiceOrders();
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/services/list.php';
            break;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $orderData = [
                    'customer_id' => $_POST['customer_id'] ?? '',
                    'brand' => $_POST['brand'] ?? '',
                    'model' => $_POST['model'] ?? '',
                    'serial_number' => $_POST['serial_number'] ?? '',
                    'warranty' => $_POST['warranty'] ?? 0,
                    'total_amount' => $_POST['total_amount'] ?? 0,
                    'prepaid_amount' => $_POST['prepaid_amount'] ?? 0,
                    'services' => $_POST['services'] ?? []
                ];

                $result = createServiceOrder($orderData);
                if ($result['success']) {
                    if ($result['prepaid_amount'] > 0) {
                        $_SESSION['pending_cash_in'] = [
                            'order_id' => $result['order_id'],
                            'order_number' => $result['order_number'],
                            'customer_name' => $result['customer_name'],
                            'amount' => $result['prepaid_amount']
                        ];
                        header('Location: ' . url('services.php?action=handle_cash_in'));
                        exit;
                    } else {
                        $_SESSION['flash_message'] = $result['message'];
                        $_SESSION['flash_type'] = 'success';
                        header('Location: ' . url('services.php?action=view&id=' . $result['order_id']));
                        exit;
                    }
                } else {
                    $error = $result['message'];
                }
            }
            $terms = getActiveTermsAndConditions();
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/services/create.php';
            break;

        case 'edit':
            if (!hasPermission('services_edit')) {
                throw new Exception("No tienes permiso para editar órdenes de servicio.");
            }
            if (!$orderId) {
                throw new Exception("ID de orden no proporcionado.");
            }
            $order = getServiceOrder($orderId);
            if (!$order) {
                throw new Exception("Orden de servicio no encontrada.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $updateData = [
                    'id' => $orderId,
                    'customer_id' => $_POST['customer_id'] ?? '',
                    'brand' => $_POST['brand'] ?? '',
                    'model' => $_POST['model'] ?? '',
                    'serial_number' => $_POST['serial_number'] ?? '',
                    'warranty' => $_POST['warranty'] ?? 0,
                    'total_amount' => $_POST['total_amount'] ?? 0,
                    'prepaid_amount' => $_POST['prepaid_amount'] ?? 0,
                    'services' => $_POST['services'] ?? []
                ];
                $result = updateServiceOrder($updateData);
                if ($result['success']) {
                    $newPrepaidAmount = floatval($_POST['prepaid_amount']);
                    $oldPrepaidAmount = floatval($result['old_prepaid_amount']);
                    $additionalPrepaid = $newPrepaidAmount - $oldPrepaidAmount;
                    
                    if ($additionalPrepaid > 0) {
                        $_SESSION['pending_cash_in'] = [
                            'order_id' => $orderId,
                            'order_number' => $order['order_number'],
                            'customer_name' => $order['customer_name'],
                            'amount' => $additionalPrepaid
                        ];
                        header('Location: ' . url('services.php?action=handle_cash_in'));
                        exit;
                    }
                    
                    $_SESSION['flash_message'] = "Orden de servicio actualizada con éxito.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('services.php?action=view&id=' . $orderId));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/services/edit.php';
            break;

        case 'handle_cash_in':
            if (!isset($_SESSION['pending_cash_in'])) {
                header('Location: ' . url('services.php'));
                exit;
            }
            
            $pendingCashIn = $_SESSION['pending_cash_in'];
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
                $notes = $_POST['notes'] ?? "Seña de {$pendingCashIn['customer_name']} de la orden {$pendingCashIn['order_number']}";
                
                $result = addCashRegisterMovement('cash_in', $amount, $notes);
                
                if ($result['success']) {
                    unset($_SESSION['pending_cash_in']);
                    $_SESSION['flash_message'] = "Ingreso de efectivo registrado exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('services.php?action=view&id=' . $pendingCashIn['order_id']));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
            
            $pageTitle = "Registrar Ingreso de Efectivo";
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/services/cash_in_modal.php';
            break;

        case 'view':
            if (!$orderId) {
                throw new Exception("ID de orden no proporcionado.");
            }
            $order = getServiceOrder($orderId);
            $order_status_history = getOrderStatusHistory($orderId);
            $order_notes = getOrderNotes($orderId);
            $order_parts = getOrderParts($orderId);
            if (!$order) {
                throw new Exception("Orden de servicio no encontrada.");
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/services/view.php';
            break;

        case 'update_status':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newStatus = $_POST['status'] ?? '';
                $statusNotes = $_POST['status_notes'] ?? '';
                if (!$orderId || !$newStatus) {
                    throw new Exception("Datos insuficientes para actualizar el estado.");
                }
                $result = updateServiceOrderStatus($orderId, $newStatus, $statusNotes, $_SESSION['user_id']);
                if ($result) {
                    $_SESSION['flash_message'] = "Estado actualizado con éxito.";
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception("Error al actualizar el estado.");
                }
                header('Location: ' . url('services.php?action=view&id=' . $orderId));
                exit;
            }
            break;

        case 'add_note':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $note = $_POST['note'] ?? '';
                $images = $_FILES['note_images'] ?? null;
                if (!$orderId || !$note) {
                    throw new Exception("Datos insuficientes para agregar la nota.");
                }
                $result = addOrderNoteWithImages($orderId, $_SESSION['user_id'], $note, $images);
                if ($result) {
                    $_SESSION['flash_message'] = "Nota agregada con éxito.";
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception("Error al agregar la nota.");
                }
                header('Location: ' . url('services.php?action=view&id=' . $orderId));
                exit;
            }
            break;

        case 'add_part':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $partData = [
                    'part_name' => $_POST['part_name'] ?? '',
                    'part_number' => $_POST['part_number'] ?? '',
                    'quantity' => $_POST['quantity'] ?? 0,
                    'cost' => $_POST['cost'] ?? 0
                ];
                if (!$orderId || empty($partData['part_name']) || empty($partData['quantity']) || empty($partData['cost'])) {
                    throw new Exception("Datos insuficientes para agregar la pieza.");
                }
                $result = addOrderPart($orderId, $partData);
                if ($result) {
                    $_SESSION['flash_message'] = "Pieza agregada con éxito.";
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception("Error al agregar la pieza.");
                }
                header('Location: ' . url('services.php?action=view&id=' . $orderId));
                exit;
            }
            break;

        case 'print':
            if (!$orderId) {
                throw new Exception("ID de orden no proporcionado.");
            }
            $order = getServiceOrder($orderId);
            $order_parts = getOrderParts($orderId);
            if (!$order) {
                throw new Exception("Orden de servicio no encontrada.");
            }
            include __DIR__ . '/../views/services/print.php';
            break;

        case 'searchCustomers':
            header('Content-Type: application/json');
            try {
                $term = $_GET['term'] ?? '';
                $customers = searchCustomers($term);
                echo json_encode($customers);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;

        case 'addCustomer':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $result = addCustomer($_POST);
                    echo json_encode($result);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            }
            exit;

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    $_SESSION['flash_message'] = $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . url('services.php'));
    exit;
}

if (!in_array($action, ['searchCustomers', 'addCustomer', 'print'])) {
    require_once __DIR__ . '/../includes/footer.php';
}