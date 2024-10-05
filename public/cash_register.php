<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/cash_register_functions.php';

if (!isLoggedIn() || !hasPermission('cash_register_manage')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'view';

switch ($action) {
    case 'open':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $openingBalance = filter_input(INPUT_POST, 'opening_balance', FILTER_VALIDATE_FLOAT);
            $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($openingBalance === false) {
                $_SESSION['flash_message'] = "El saldo inicial no es válido.";
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = openCashRegister($openingBalance, $notes);
                if ($result['success']) {
                    $_SESSION['flash_message'] = "Caja abierta exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('cash_register.php'));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
        }
        $pageTitle = "Abrir Caja";
        include __DIR__ . '/../views/cash_register/open.php';
        break;

    case 'close':
        $currentSession = getCurrentCashRegisterSession();
        if (!$currentSession) {
            $_SESSION['flash_message'] = "No hay una sesión de caja abierta para cerrar.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('cash_register.php'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $closingBalance = filter_input(INPUT_POST, 'closing_balance', FILTER_VALIDATE_FLOAT);
            $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($closingBalance === false) {
                $_SESSION['flash_message'] = "El saldo final no es válido.";
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = closeCashRegister($closingBalance, $notes);
                if ($result['success']) {
                    $_SESSION['flash_message'] = "Caja cerrada exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('cash_register.php'));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
        }

        $pageTitle = "Cerrar Caja";
        $movements = getCashRegisterMovements($currentSession['id']);
        $purchases = getPurchasesForSession($currentSession['id']);
        $summary = getCashRegisterSummary($currentSession['id']);
        include __DIR__ . '/../views/cash_register/close.php';
        break;

    case 'movement':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movementType = filter_input(INPUT_POST, 'movement_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
            $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($amount === false) {
                $_SESSION['flash_message'] = "El monto no es válido.";
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = addCashRegisterMovement($movementType, $amount, $notes);
                if ($result['success']) {
                    $_SESSION['flash_message'] = "Movimiento registrado exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('cash_register.php'));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
        }
        $pageTitle = "Registrar Movimiento";
        include __DIR__ . '/../views/cash_register/movement.php';
        break;

    case 'edit':
        if (!hasPermission('cash_register_edit')) {
            $_SESSION['flash_message'] = "No tienes permiso para editar movimientos de caja.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('cash_register.php'));
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (!$id || !$date) {
            $_SESSION['flash_message'] = "ID de movimiento o fecha inválidos.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('cash_register.php'));
            exit;
        }

        $movement = getMovementById($id);
        if (!$movement) {
            $_SESSION['flash_message'] = "Movimiento no encontrado.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('cash_register.php'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movementType = filter_input(INPUT_POST, 'movement_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
            $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if ($amount === false) {
                $_SESSION['flash_message'] = "El monto no es válido.";
                $_SESSION['flash_type'] = 'error';
            } else {
                $result = updateCashRegisterMovement($id, $movementType, $amount, $notes);
                if ($result['success']) {
                    $_SESSION['flash_message'] = "Movimiento actualizado exitosamente.";
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('cash_register.php'));
                    exit;
                } else {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'error';
                }
            }
        }

        $pageTitle = "Editar Movimiento";
        include __DIR__ . '/../views/cash_register/edit_movement.php';
        break;

    default:
        $pageTitle = "Estado de Caja";
        $currentSession = getCurrentCashRegisterSession();
        if ($currentSession) {
            $movements = getCashRegisterMovements($currentSession['id']);
            $purchases = getPurchasesForSession($currentSession['id']);
            $summary = getCashRegisterSummary($currentSession['id']);
        } else {
            $movements = [];
            $purchases = [];
            $summary = null;
        }
        include __DIR__ . '/../views/cash_register/view.php';
        break;
}
?>