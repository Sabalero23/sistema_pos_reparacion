<?php
require_once __DIR__ . '/../config/config.php';

if (!function_exists('getCurrentCashRegisterSession')) {
    function getCurrentCashRegisterSession() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM cash_register_sessions 
                               WHERE user_id = ? AND status = 'open' 
                               ORDER BY opening_date DESC LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('openCashRegister')) {
    function openCashRegister($openingBalance, $notes = '') {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO cash_register_sessions 
                                   (user_id, opening_date, opening_balance, notes) 
                                   VALUES (?, NOW(), ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $openingBalance, $notes]);
            return ['success' => true, 'message' => 'Caja abierta exitosamente.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al abrir la caja: ' . $e->getMessage()];
        }
    }
}

function closeCashRegister($closingBalance, $notes = '') {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        $currentSession = getCurrentCashRegisterSession();
        if (!$currentSession) {
            throw new Exception("No hay una sesión de caja abierta para cerrar.");
        }
        
        $stmt = $pdo->prepare("UPDATE cash_register_sessions 
                               SET closing_date = NOW(), 
                                   closing_balance = ?, 
                                   status = 'closed', 
                                   notes = CONCAT(IFNULL(notes, ''), '\n', ?) 
                               WHERE id = ?");
        $stmt->execute([$closingBalance, $notes, $currentSession['id']]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Caja cerrada exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function addCashRegisterMovement($movementType, $amount, $notes, $cashRegisterSessionId = null) {
    global $pdo;
    try {
        if ($cashRegisterSessionId === null) {
            $currentSession = getCurrentCashRegisterSession();
            if (!$currentSession) {
                return ['success' => false, 'message' => 'No hay una sesión de caja abierta.'];
            }
            $cashRegisterSessionId = $currentSession['id'];
        }

        $stmt = $pdo->prepare("INSERT INTO cash_register_movements (cash_register_session_id, movement_type, amount, notes, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$cashRegisterSessionId, $movementType, $amount, $notes]);
        return ['success' => true, 'message' => 'Movimiento registrado exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Error al registrar el movimiento: " . $e->getMessage()];
    }
}

function getCashRegisterMovements($cashRegisterSessionId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 'sale' as movement_type, total_amount as amount, sale_date as created_at, CONCAT('Venta #', id) as notes
        FROM sales
        WHERE cash_register_session_id = :cash_register_session_id
        UNION ALL
        SELECT 'purchase' as movement_type, total_amount as amount, purchase_date as created_at, CONCAT('Compra #', id) as notes
        FROM purchases
        WHERE cash_register_session_id = :cash_register_session_id
        UNION ALL
        SELECT movement_type, amount, created_at, notes
        FROM cash_register_movements
        WHERE cash_register_session_id = :cash_register_session_id
        ORDER BY created_at DESC
    ");
    $stmt->execute(['cash_register_session_id' => $cashRegisterSessionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateCashRegisterBalance($cashRegisterSessionId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 
                               opening_balance + 
                               COALESCE(SUM(CASE 
                                   WHEN movement_type IN ('sale', 'cash_in') THEN amount 
                                   WHEN movement_type IN ('purchase', 'cash_out') THEN -amount 
                                   ELSE 0 
                               END), 0) as current_balance 
                           FROM cash_register_sessions 
                           LEFT JOIN cash_register_movements ON cash_register_sessions.id = cash_register_movements.cash_register_session_id 
                           WHERE cash_register_sessions.id = ?");
    $stmt->execute([$cashRegisterSessionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['current_balance'];
}

function getCashRegisterHistory($limit = 10, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 
                               crs.*, 
                               u.name as user_name, 
                               (SELECT SUM(CASE 
                                   WHEN movement_type IN ('sale', 'cash_in') THEN amount 
                                   WHEN movement_type IN ('purchase', 'cash_out') THEN -amount 
                                   ELSE 0 
                               END) 
                               FROM cash_register_movements 
                               WHERE cash_register_session_id = crs.id) as total_movements 
                           FROM cash_register_sessions crs 
                           JOIN users u ON crs.user_id = u.id 
                           ORDER BY crs.opening_date DESC 
                           LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validateCashRegisterOperation($operation, $amount) {
    if (!is_numeric($amount) || $amount <= 0) {
        return ['success' => false, 'message' => 'El monto debe ser un número positivo.'];
    }
    
    $currentSession = getCurrentCashRegisterSession();
    if (!$currentSession) {
        return ['success' => false, 'message' => 'No hay una sesión de caja abierta.'];
    }
    
    $currentBalance = calculateCashRegisterBalance($currentSession['id']);
    
    if ($operation == 'cash_out' && $amount > $currentBalance) {
        return ['success' => false, 'message' => 'No hay suficiente saldo en la caja para realizar esta operación.'];
    }
    
    return ['success' => true];
}

function performCashOperation($operation, $amount, $notes = '') {
    $validation = validateCashRegisterOperation($operation, $amount);
    if (!$validation['success']) {
        return $validation;
    }
    
    $result = addCashRegisterMovement($operation, $amount, $notes);
    
    if ($result['success']) {
        return ['success' => true, 'message' => 'Operación realizada exitosamente.'];
    } else {
        return ['success' => false, 'message' => 'Error al realizar la operación: ' . $result['message']];
    }
}

function getCashRegisterSummary($sessionId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 
                               crs.*, 
                               u.name as user_name,
                               COALESCE(SUM(CASE WHEN crm.movement_type = 'sale' THEN crm.amount ELSE 0 END), 0) as total_sales,
                               COALESCE((SELECT SUM(total_amount) FROM purchases WHERE cash_register_session_id = crs.id), 0) as total_purchases,
                               COALESCE(SUM(CASE WHEN crm.movement_type = 'cash_in' THEN crm.amount ELSE 0 END), 0) as total_cash_in,
                               COALESCE(SUM(CASE WHEN crm.movement_type = 'cash_out' THEN crm.amount ELSE 0 END), 0) as total_cash_out
                           FROM cash_register_sessions crs 
                           LEFT JOIN cash_register_movements crm ON crs.id = crm.cash_register_session_id
                           JOIN users u ON crs.user_id = u.id 
                           WHERE crs.id = ?
                           GROUP BY crs.id");
    $stmt->execute([$sessionId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPurchasesForSession($sessionId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, total_amount FROM purchases WHERE cash_register_session_id = :sessionId");
    $stmt->execute([':sessionId' => $sessionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>