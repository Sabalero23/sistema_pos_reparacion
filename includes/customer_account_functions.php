<?php
function getAllCustomerAccounts() {
    global $pdo;
    $query = "SELECT 
                c.id, 
                c.name, 
                COALESCE(SUM(s.total_amount), 0) as total_sales,
                COALESCE(SUM(p.amount), 0) as total_payments,
                COALESCE(SUM(s.total_amount), 0) - COALESCE(SUM(p.amount), 0) as balance,
                MAX(s.sale_date) as last_sale_date,
                MAX(p.payment_date) as last_payment_date
              FROM customers c
              LEFT JOIN sales s ON c.id = s.customer_id
              LEFT JOIN payments p ON c.id = p.customer_id
              WHERE c.name != 'Consumidor Final'
              GROUP BY c.id
              HAVING total_sales > 0
              ORDER BY balance DESC, total_sales DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerAccount($customerId) {
    global $pdo;
    $query = "SELECT 
                c.id, 
                c.name, 
                COALESCE(SUM(s.total_amount), 0) as total_sales,
                COALESCE(SUM(p.amount), 0) as total_payments,
                COALESCE(SUM(s.total_amount), 0) - COALESCE(SUM(p.amount), 0) as balance
              FROM customers c
              LEFT JOIN sales s ON c.id = s.customer_id
              LEFT JOIN payments p ON c.id = p.customer_id
              WHERE c.id = :customer_id
              GROUP BY c.id, c.name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':customer_id' => $customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCustomerSales($customerId) {
    global $pdo;
    $query = "SELECT 
                s.id, 
                s.sale_date, 
                s.total_amount, 
                COALESCE(SUM(sp.amount), 0) as amount_paid,
                s.total_amount - COALESCE(SUM(sp.amount), 0) as balance,
                s.status
              FROM sales s
              LEFT JOIN sale_payments sp ON s.id = sp.sale_id
              WHERE s.customer_id = :customer_id
              GROUP BY s.id
              ORDER BY s.sale_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':customer_id' => $customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addPayment($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // Validar datos de entrada
        if (empty($data['customer_id']) || empty($data['amount']) || empty($data['payment_method'])) {
            throw new Exception("Faltan datos requeridos para el pago.");
        }
        
        // Insertar el pago
        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, amount, payment_date, payment_method, notes) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->execute([$data['customer_id'], $data['amount'], $data['payment_method'], $data['notes'] ?? null]);
        $paymentId = $pdo->lastInsertId();
        
        // Obtener las ventas pendientes del cliente
        $stmt = $pdo->prepare("
            SELECT 
                s.id, 
                s.total_amount, 
                COALESCE((SELECT SUM(amount) FROM sale_payments WHERE sale_id = s.id), 0) as amount_paid
            FROM 
                sales s
            WHERE 
                s.customer_id = ? AND s.total_amount > (SELECT COALESCE(SUM(amount), 0) FROM sale_payments WHERE sale_id = s.id)
            ORDER BY 
                s.sale_date ASC
        ");
        $stmt->execute([$data['customer_id']]);
        $pendingSales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $remainingAmount = $data['amount'];
        foreach ($pendingSales as $sale) {
            $pendingAmount = $sale['total_amount'] - $sale['amount_paid'];
            $amountToApply = min($remainingAmount, $pendingAmount);
            
            if ($amountToApply > 0) {
                // Registrar el pago para esta venta
                $stmt = $pdo->prepare("INSERT INTO sale_payments (sale_id, payment_id, amount) VALUES (?, ?, ?)");
                $stmt->execute([$sale['id'], $paymentId, $amountToApply]);
                
                $remainingAmount -= $amountToApply;
                
                if ($remainingAmount <= 0) {
                    break;
                }
            }
        }
        
        // Si queda algún remanente, lo registramos como crédito del cliente
        if ($remainingAmount > 0) {
            $stmt = $pdo->prepare("UPDATE customers SET credit_balance = COALESCE(credit_balance, 0) + ? WHERE id = ?");
            $stmt->execute([$remainingAmount, $data['customer_id']]);
        }
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Pago registrado correctamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error adding payment: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()];
    }
}

function getPayments($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE customer_id = :customer_id ORDER BY payment_date DESC");
    $stmt->execute([':customer_id' => $customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPaymentById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCustomerById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSaleBalance($saleId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.total_amount, COALESCE(SUM(sp.amount), 0) as amount_paid
        FROM sales s
        LEFT JOIN sale_payments sp ON s.id = sp.sale_id
        WHERE s.id = ?
        GROUP BY s.id
    ");
    $stmt->execute([$saleId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return $result['total_amount'] - $result['amount_paid'];
    }
    
    return 0;
}

function getCustomerAccountDetail($customerId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.name,
                s.id as sale_id,
                s.sale_date,
                s.total_amount as sale_amount,
                COALESCE(sp.paid_amount, 0) as paid_amount,
                s.total_amount - COALESCE(sp.paid_amount, 0) as balance
            FROM 
                customers c
            LEFT JOIN 
                sales s ON c.id = s.customer_id
            LEFT JOIN 
                (SELECT sale_id, SUM(amount) as paid_amount FROM sale_payments GROUP BY sale_id) sp ON s.id = sp.sale_id
            WHERE 
                c.id = ?
            ORDER BY 
                s.sale_date
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting customer account detail: " . $e->getMessage());
        return [];
    }
}
?>