<?php
// Asegúrate de que la conexión a la base de datos esté disponible
global $pdo;

function getAllCustomerAccounts() {
    global $pdo;
    $query = "SELECT ca.*, c.name as customer_name, c.phone as customer_phone,
                     (SELECT COUNT(*) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as pending_installments,
                     (SELECT MIN(due_date) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as next_due_date
              FROM customer_accounts ca 
              JOIN customers c ON ca.customer_id = c.id 
              ORDER BY ca.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCustomerAccount($accountId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT ca.*, c.name as customer_name 
                           FROM customer_accounts ca 
                           JOIN customers c ON ca.customer_id = c.id 
                           WHERE ca.id = ?");
    $stmt->execute([$accountId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getAccountInstallments($accountId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM installments WHERE account_id = ? ORDER BY installment_number ASC");
    $stmt->execute([$accountId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAccountPayments($accountId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE account_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$accountId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function addCustomerAccount($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO customer_accounts (customer_id, total_amount, down_payment, num_installments, first_due_date, description) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['total_amount'],
            $data['down_payment'],
            $data['num_installments'],
            $data['first_due_date'],
            $data['description']
        ]);

        $accountId = $pdo->lastInsertId();

        // Crear las cuotas
        $installmentAmount = ($data['total_amount'] - $data['down_payment']) / $data['num_installments'];
        $dueDate = new DateTime($data['first_due_date']);

        for ($i = 1; $i <= $data['num_installments']; $i++) {
            $stmt = $pdo->prepare("INSERT INTO installments (account_id, installment_number, amount, due_date) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $accountId,
                $i,
                $installmentAmount,
                $dueDate->format('Y-m-d')
            ]);

            $dueDate->modify('+1 month');
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Cuenta de cliente creada exitosamente'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al crear la cuenta: ' . $e->getMessage()];
    }
}


function updateCustomerAccount($accountId, $data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE customer_accounts SET 
                               total_amount = ?, down_payment = ?, num_installments = ?, 
                               first_due_date = ?, description = ? 
                               WHERE id = ?");
        $stmt->execute([
            $data['total_amount'],
            $data['down_payment'],
            $data['num_installments'],
            $data['first_due_date'],
            $data['description'],
            $accountId
        ]);

        // Actualizar o crear nuevas cuotas
        // (Esta parte puede ser compleja y depende de cómo quieras manejar los cambios en las cuotas)

        $pdo->commit();
        return ['success' => true, 'message' => 'Cuenta de cliente actualizada exitosamente'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al actualizar la cuenta: ' . $e->getMessage()];
    }
}

function processPayment($installmentId, $amount, $paymentDate, $paymentMethod, $notes) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        // Obtener información de la cuota
        $stmt = $pdo->prepare("SELECT i.id, i.account_id, i.amount, i.status, ca.balance, ca.customer_id,
                               (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE installment_id = i.id) as paid_amount
                               FROM installments i
                               JOIN customer_accounts ca ON i.account_id = ca.id
                               WHERE i.id = ?");
        $stmt->execute([$installmentId]);
        $installment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$installment) {
            throw new Exception("No se encontró la cuota especificada.");
        }
        
        $remainingAmount = $installment['amount'] - $installment['paid_amount'];
        
        if ($amount > $remainingAmount) {
            throw new Exception("El monto del pago excede el saldo pendiente de la cuota.");
        }

        // Insertar el pago
        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, account_id, installment_id, amount, payment_date, payment_method, notes) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $installment['customer_id'],
            $installment['account_id'],
            $installmentId,
            $amount,
            $paymentDate,
            $paymentMethod,
            $notes
        ]);
        $paymentId = $pdo->lastInsertId();
        
        // Actualizar el estado de la cuota
        $newPaidAmount = $installment['paid_amount'] + $amount;
        $newStatus = ($newPaidAmount >= $installment['amount']) ? 'pagada' : 'parcial';
        $stmt = $pdo->prepare("UPDATE installments SET status = ?, paid_date = CASE WHEN ? = 'pagada' THEN ? ELSE paid_date END WHERE id = ?");
        $stmt->execute([$newStatus, $newStatus, $paymentDate, $installmentId]);
        
        // Actualizar el balance de la cuenta del cliente
        $newBalance = $installment['balance'] - $amount;
        $stmt = $pdo->prepare("UPDATE customer_accounts SET balance = ?, last_payment_date = ? WHERE id = ?");
        $stmt->execute([$newBalance, $paymentDate, $installment['account_id']]);

        // Actualizar el estado de la cuenta
        updateAccountStatus($installment['account_id']);

        $pdo->commit();
        return ['success' => true, 'message' => 'Pago procesado exitosamente', 'paymentId' => $paymentId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()];
    }
}
function updateAccountStatus($accountId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) as overdue_count 
                           FROM installments 
                           WHERE account_id = ? AND status != 'pagada' AND due_date < CURDATE()");
    $stmt->execute([$accountId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $newStatus = $result['overdue_count'] > 0 ? 'atrasada' : 'al_dia';

    $stmt = $pdo->prepare("UPDATE customer_accounts SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $accountId]);
}

function getPendingInstallments($accountId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT i.*, ca.customer_id
                           FROM installments i
                           JOIN customer_accounts ca ON i.account_id = ca.id
                           WHERE i.account_id = ? AND i.status != 'pagada'
                           ORDER BY i.due_date ASC");
    $stmt->execute([$accountId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene los clientes con cuotas vencidas o próximas a vencer.
 *
 * @return array Lista de clientes con problemas de pago
 */
function getClientsWithOverdueOrUpcomingInstallments() {
    global $pdo;
    
    $currentDate = date('Y-m-d');
    $upcomingDate = date('Y-m-d', strtotime('+7 days')); // Consideramos próximas las cuotas que vencen en los próximos 7 días
    
    $query = "SELECT c.id, c.name, 
                     COUNT(CASE WHEN i.due_date < :currentDate AND i.status != 'pagada' THEN 1 END) as overdue_installments,
                     MIN(CASE WHEN i.due_date >= :currentDate AND i.status != 'pagada' THEN i.due_date END) as next_due_date
              FROM customers c
              JOIN customer_accounts ca ON c.id = ca.customer_id
              JOIN installments i ON ca.id = i.account_id
              WHERE (i.due_date < :upcomingDate AND i.status != 'pagada')
              GROUP BY c.id, c.name
              HAVING overdue_installments > 0 OR next_due_date IS NOT NULL
              ORDER BY overdue_installments DESC, next_due_date ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':currentDate' => $currentDate,
        ':upcomingDate' => $upcomingDate
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function saveAccessToken($accountId, $token) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE customer_accounts SET access_token = :token WHERE id = :account_id");
        
        $stmt->execute([
            ':account_id' => $accountId,
            ':token' => $token
        ]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Aquí podrías loguear el error si lo deseas
        error_log("Error al guardar el token de acceso: " . $e->getMessage());
        return false;
    }
}

function getAccountByToken($token) {
    global $pdo;
    
    $query = "SELECT ca.*, c.name as customer_name,
              (SELECT COUNT(*) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as pending_installments,
              (SELECT MIN(due_date) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as next_due_date
              FROM customer_accounts ca 
              JOIN customers c ON ca.customer_id = c.id 
              WHERE ca.access_token = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$token]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


?>