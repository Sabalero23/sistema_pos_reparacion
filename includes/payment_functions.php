<?php
function getAllPayments() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, c.name as customer_name 
                         FROM payments p 
                         JOIN customers c ON p.customer_id = c.id 
                         ORDER BY p.payment_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPaymentById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as customer_name 
                           FROM payments p 
                           JOIN customers c ON p.customer_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createPayment($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, amount, payment_date, payment_method, notes) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_method'],
            $data['notes']
        ]);
        
        updateCustomerAccountBalance($data['customer_id'], -$data['amount']);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Pago registrado correctamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()];
    }
}

function applyPaymentToSales($customerId, $amount) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, balance FROM sales 
                           WHERE customer_id = ? AND balance > 0 
                           ORDER BY sale_date ASC");
    $stmt->execute([$customerId]);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $remainingAmount = $amount;
    foreach ($sales as $sale) {
        if ($remainingAmount <= 0) break;
        
        $paymentForSale = min($remainingAmount, $sale['balance']);
        $newBalance = $sale['balance'] - $paymentForSale;
        
        $updateStmt = $pdo->prepare("UPDATE sales SET amount_paid = amount_paid + ?, balance = ? WHERE id = ?");
        $updateStmt->execute([$paymentForSale, $newBalance, $sale['id']]);
        
        $remainingAmount -= $paymentForSale;
    }
}
?>