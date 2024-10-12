<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/cash_register_functions.php';

global $pdo;

if (!isLoggedIn() || !hasPermission('cash_register_manage')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'list';
$date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? date('Y-m-d');

switch ($action) {
    case 'edit':
        $movementId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$movementId) {
            $_SESSION['flash_message'] = "ID de movimiento inválido.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('cash_register_daily.php'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
            $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $movementType = filter_input(INPUT_POST, 'movement_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($amount === false) {
                $_SESSION['flash_message'] = "El monto no es válido.";
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = updateCashRegisterMovement($pdo, $movementId, $amount, $notes, $movementType);
                if ($result['success']) {
                    $_SESSION['flash_message'] = "Movimiento actualizado exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('cash_register_daily.php?date=' . $date));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
        }

        $movement = getCashRegisterMovementById($pdo, $movementId);
        $pageTitle = "Editar Movimiento";
        include __DIR__ . '/../views/cash_register/edit_movement.php';
        break;

    default:
        $pageTitle = "Movimientos Diarios de Caja";
        $movements = getDailyCashRegisterMovements($pdo, $date);
        $summary = getDailyCashRegisterSummary($pdo, $date);
        include __DIR__ . '/../views/cash_register/daily_list.php';
        break;
}

function getDailyCashRegisterMovements($pdo, $date) {
    $sql = "SELECT crm.*, crs.opening_date, crs.closing_date, u.name as user_name
            FROM cash_register_movements crm
            JOIN cash_register_sessions crs ON crm.cash_register_session_id = crs.id
            JOIN users u ON crs.user_id = u.id
            WHERE DATE(crm.created_at) = ?
            ORDER BY crm.created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDailyCashRegisterSummary($pdo, $date) {
    $sql = "SELECT 
                SUM(CASE WHEN movement_type IN ('sale', 'cash_in') THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN movement_type IN ('purchase', 'cash_out') THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN movement_type IN ('sale', 'cash_in') THEN amount ELSE -amount END) as balance
            FROM cash_register_movements
            WHERE DATE(created_at) = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCashRegisterMovementById($pdo, $id) {
    $sql = "SELECT * FROM cash_register_movements WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateCashRegisterMovement($pdo, $id, $amount, $notes, $movementType) {
    try {
        $pdo->beginTransaction();

        $sql = "UPDATE cash_register_movements SET amount = ?, notes = ?, movement_type = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$amount, $notes, $movementType, $id]);

        $pdo->commit();
        return ['success' => true];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al actualizar el movimiento: ' . $e->getMessage()];
    }
}
?>