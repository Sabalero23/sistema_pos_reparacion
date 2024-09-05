<?php
require_once __DIR__ . '/../config/config.php';
global $pdo;
function getAllCustomerAccounts() {
    global $pdo;
    try {
        $query = "SELECT ca.*, c.name as customer_name,
                         (SELECT COUNT(*) FROM installments 
                          WHERE account_id = ca.id AND status != 'pagada') as pending_installments,
                         (SELECT MIN(due_date) FROM installments 
                          WHERE account_id = ca.id AND status != 'pagada') as next_due_date
                         FROM customer_accounts ca 
                         JOIN customers c ON ca.customer_id = c.id 
                         ORDER BY ca.id DESC";
        
        error_log("Ejecutando consulta: " . $query);
        
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Número de resultados: " . count($results));
        
        return $results;
    } catch (PDOException $e) {
        error_log("Error en getAllCustomerAccounts: " . $e->getMessage());
        return false;
    }
}

function getAllCustomers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en getAllCustomers: " . $e->getMessage());
        return false;
    }
}

function getCustomerAccount($accountId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT ca.*, c.name as customer_name 
                           FROM customer_accounts ca 
                           JOIN customers c ON ca.customer_id = c.id 
                           WHERE ca.id = ?");
    $stmt->execute([$accountId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($account) {
        $account['installments'] = getInstallments($accountId);
    }
    
    return $account;
}

function addPayment($data) {
    global $pdo;
    
    error_log("Iniciando addPayment con datos: " . print_r($data, true));
    
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        error_log("Error en addPayment: La conexión a la base de datos no es válida");
        return ['success' => false, 'message' => 'Error de conexión a la base de datos'];
    }

    try {
        $pdo->beginTransaction();

        // Validar datos de entrada
        if (!isset($data['customer_id'], $data['account_id'], $data['amount'], $data['payment_date'], $data['payment_method'])) {
            throw new Exception("Faltan datos requeridos para el pago.");
        }

        // Insertar el pago en la tabla payments
        $sql = "INSERT INTO payments (customer_id, account_id, amount, payment_date, payment_method, notes) 
                VALUES (:customer_id, :account_id, :amount, :payment_date, :payment_method, :notes)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':customer_id' => $data['customer_id'],
            ':account_id' => $data['account_id'],
            ':amount' => $data['amount'],
            ':payment_date' => $data['payment_date'],
            ':payment_method' => $data['payment_method'],
            ':notes' => $data['notes'] ?? null
        ]);

        if (!$result) {
            throw new Exception("Error al insertar el pago: " . print_r($stmt->errorInfo(), true));
        }

        $paymentId = $pdo->lastInsertId();
        error_log("Pago insertado con ID: " . $paymentId);

        // Actualizar el saldo de la cuenta
        $sql = "UPDATE customer_accounts 
                SET balance = balance - :amount, 
                    last_payment_date = :payment_date 
                WHERE id = :account_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':amount' => $data['amount'],
            ':payment_date' => $data['payment_date'],
            ':account_id' => $data['account_id']
        ]);

        if (!$result) {
            throw new Exception("Error al actualizar el saldo de la cuenta: " . print_r($stmt->errorInfo(), true));
        }

        error_log("Saldo de cuenta actualizado");

        // Actualizar el estado de la cuota si es necesario
        if (isset($data['installment_id'])) {
            $sql = "UPDATE installments 
                    SET status = CASE 
                        WHEN (SELECT SUM(amount) FROM payments WHERE account_id = :account_id) >= amount THEN 'pagada' 
                        ELSE 'parcial' 
                    END 
                    WHERE id = :installment_id";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':account_id' => $data['account_id'],
                ':installment_id' => $data['installment_id']
            ]);

            if (!$result) {
                throw new Exception("Error al actualizar el estado de la cuota: " . print_r($stmt->errorInfo(), true));
            }

            error_log("Estado de cuota actualizado");
        }

        $pdo->commit();
        error_log("Transacción completada con éxito");
        return ['success' => true, 'message' => 'Pago registrado con éxito.', 'payment_id' => $paymentId];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error en addPayment: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        return ['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()];
    }
}

function updateCustomerAccount($accountId, $data) {
    global $pdo;
    
    $errors = validateCustomerAccountData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE customer_accounts 
                               SET total_amount = ?, down_payment = ?, balance = ?, num_installments = ?, first_due_date = ?, description = ? 
                               WHERE id = ?");
        $balance = $data['total_amount'] - $data['down_payment'];
        $stmt->execute([
            $data['total_amount'],
            $data['down_payment'],
            $balance,
            $data['num_installments'],
            $data['first_due_date'],
            $data['description'],
            $accountId
        ]);

        // Eliminar cuotas existentes
        $stmt = $pdo->prepare("DELETE FROM installments WHERE account_id = ?");
        $stmt->execute([$accountId]);

        // Crear nuevas cuotas
        $installmentAmount = $balance / $data['num_installments'];
        $dueDate = new DateTime($data['first_due_date']);
        
        for ($i = 1; $i <= $data['num_installments']; $i++) {
            $stmt = $pdo->prepare("INSERT INTO installments (account_id, installment_number, amount, due_date, status) 
                                   VALUES (?, ?, ?, ?, 'pendiente')");
            $stmt->execute([$accountId, $i, $installmentAmount, $dueDate->format('Y-m-d')]);
            $dueDate->add(new DateInterval('P30D')); // Añadir 30 días para la próxima cuota
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Cuenta de cliente actualizada exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al actualizar la cuenta de cliente: ' . $e->getMessage()];
    }
}



function updateInstallmentStatus($accountId, $paymentAmount) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM installments 
                           WHERE account_id = ? AND status != 'pagada' 
                           ORDER BY due_date ASC");
    $stmt->execute([$accountId]);
    $installments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($installments as $installment) {
        if ($paymentAmount >= $installment['amount']) {
            $stmt = $pdo->prepare("UPDATE installments SET status = 'pagada' WHERE id = ?");
            $stmt->execute([$installment['id']]);
            $paymentAmount -= $installment['amount'];
        } elseif ($paymentAmount > 0) {
            $stmt = $pdo->prepare("UPDATE installments SET status = 'parcial' WHERE id = ?");
            $stmt->execute([$installment['id']]);
            break;
        } else {
            break;
        }
    }
}

function addLateFee($installmentId, $feeAmount) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        // Actualizar el monto de la cuota
        $stmt = $pdo->prepare("UPDATE installments SET amount = amount + ? WHERE id = ?");
        $stmt->execute([$feeAmount, $installmentId]);

        // Actualizar el saldo de la cuenta
        $stmt = $pdo->prepare("UPDATE customer_accounts ca
                               JOIN installments i ON ca.id = i.account_id
                               SET ca.balance = ca.balance + ?
                               WHERE i.id = ?");
        $stmt->execute([$feeAmount, $installmentId]);

        // Registrar el cargo por mora
        $stmt = $pdo->prepare("INSERT INTO late_fees (installment_id, amount, date_applied) 
                               VALUES (?, ?, CURDATE())");
        $stmt->execute([$installmentId, $feeAmount]);

        $pdo->commit();
        return ['success' => true, 'message' => 'Cargo por mora aplicado exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al aplicar el cargo por mora: ' . $e->getMessage()];
    }
}

function getCustomerSales($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM sales WHERE customer_id = ? ORDER BY sale_date DESC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPayments($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.* FROM payments p
                           JOIN customer_accounts ca ON p.account_id = ca.id
                           WHERE ca.customer_id = ? 
                           ORDER BY p.payment_date DESC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getInstallments($accountId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM installments WHERE account_id = ? ORDER BY due_date");
    $stmt->execute([$accountId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validateCustomerAccountData($data) {
    $errors = [];

    if (empty($data['customer_id'])) {
        $errors[] = "Debe seleccionar un cliente.";
    }

    if (!is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
        $errors[] = "El monto total debe ser un número positivo.";
    }

    if (!is_numeric($data['down_payment']) || $data['down_payment'] < 0) {
        $errors[] = "La entrega inicial debe ser un número no negativo.";
    }

    if (!is_numeric($data['num_installments']) || $data['num_installments'] <= 0) {
        $errors[] = "El número de cuotas debe ser un número positivo.";
    }

    if (empty($data['first_due_date'])) {
        $errors[] = "Debe especificar la fecha de vencimiento de la primera cuota.";
    } elseif (strtotime($data['first_due_date']) < strtotime(date('Y-m-d'))) {
        $errors[] = "La fecha de vencimiento de la primera cuota no puede ser en el pasado.";
    }

    return $errors;
}

function validatePaymentData($data) {
    $errors = [];

    if (empty($data['account_id'])) {
        $errors[] = "Debe especificar la cuenta del cliente.";
    }

    if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
        $errors[] = "El monto del pago debe ser un número positivo.";
    }

    if (empty($data['payment_date'])) {
        $errors[] = "Debe especificar la fecha del pago.";
    } elseif (strtotime($data['payment_date']) > strtotime(date('Y-m-d'))) {
        $errors[] = "La fecha del pago no puede ser en el futuro.";
    }

    if (empty($data['payment_method'])) {
        $errors[] = "Debe especificar el método de pago.";
    }

    return $errors;
}

function applyLateFees() {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT i.id, i.amount, i.account_id
            FROM installments i
            LEFT JOIN late_fees lf ON i.id = lf.installment_id AND lf.date_applied = CURDATE()
            WHERE i.due_date < CURDATE() AND i.status != 'pagada' AND lf.id IS NULL
        ");
        $stmt->execute();
        $lateInstallments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lateInstallments as $installment) {
            $lateFee = $installment['amount'] * 0.05; // 5% de cargo por mora
            
            $stmt = $pdo->prepare("INSERT INTO late_fees (installment_id, amount, date_applied) VALUES (?, ?, CURDATE())");
            $stmt->execute([$installment['id'], $lateFee]);

            $stmt = $pdo->prepare("UPDATE installments SET amount = amount + ? WHERE id = ?");
            $stmt->execute([$lateFee, $installment['id']]);

            $stmt = $pdo->prepare("UPDATE customer_accounts SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$lateFee, $installment['account_id']]);
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Cargos por mora aplicados exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al aplicar cargos por mora: ' . $e->getMessage()];
    }
}

function createCustomerAccount($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO customer_accounts (customer_id, total_amount, down_payment, balance, num_installments, first_due_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $balance = $data['total_amount'] - $data['down_payment'];
        $stmt->execute([
            $data['customer_id'],
            $data['total_amount'],
            $data['down_payment'],
            $balance,
            $data['num_installments'],
            $data['first_due_date'],
            $data['description']
        ]);

        $accountId = $pdo->lastInsertId();

        // Crear las cuotas
        $installmentAmount = $balance / $data['num_installments'];
        $dueDate = new DateTime($data['first_due_date']);
        
        for ($i = 1; $i <= $data['num_installments']; $i++) {
            $stmt = $pdo->prepare("INSERT INTO installments (account_id, installment_number, amount, due_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$accountId, $i, $installmentAmount, $dueDate->format('Y-m-d')]);
            $dueDate->add(new DateInterval('P30D')); // Añadir 30 días para la próxima cuota
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Cuenta de cliente creada exitosamente.', 'account_id' => $accountId];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al crear la cuenta de cliente: ' . $e->getMessage()];
    }
}

function registerPayment($data) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, account_id, amount, payment_date, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['account_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_method'],
            $data['notes']
        ]);

        // Actualizar el saldo de la cuenta
        $stmt = $pdo->prepare("UPDATE customer_accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$data['amount'], $data['account_id']]);

        // Actualizar el estado de las cuotas
        updateInstallmentStatus($data['account_id'], $data['amount']);

        $pdo->commit();
        return ['success' => true, 'message' => 'Pago registrado exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al registrar el pago: ' . $e->getMessage()];
    }
}

function getPaymentsByCustomerId($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE customer_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPendingInstallments($accountId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, installment_number, amount, due_date 
                               FROM installments 
                               WHERE account_id = ? AND status != 'pagada'
                               ORDER BY due_date ASC");
        $stmt->execute([$accountId]);
        $installments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($installments)) {
            return ['success' => false, 'message' => 'No se encontraron cuotas pendientes.'];
        }
        
        // Formatear las fechas y los montos para la visualización
        foreach ($installments as &$installment) {
            $installment['due_date'] = date('d/m/Y', strtotime($installment['due_date']));
            $installment['amount'] = number_format($installment['amount'], 2);
        }
        
        return ['success' => true, 'data' => $installments];
    } catch (PDOException $e) {
        error_log("Error al obtener cuotas pendientes: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al obtener cuotas pendientes: ' . $e->getMessage()];
    }
}

function addCustomerAccount($data) {
    global $pdo;
    
    $errors = validateCustomerAccountData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO customer_accounts (customer_id, total_amount, down_payment, balance, num_installments, first_due_date, description) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $balance = $data['total_amount'] - $data['down_payment'];
        $stmt->execute([
            $data['customer_id'],
            $data['total_amount'],
            $data['down_payment'],
            $balance,
            $data['num_installments'],
            $data['first_due_date'],
            $data['description']
        ]);

        $accountId = $pdo->lastInsertId();

        // Crear las cuotas
        $installmentAmount = $balance / $data['num_installments'];
        $dueDate = new DateTime($data['first_due_date']);
        
        for ($i = 1; $i <= $data['num_installments']; $i++) {
            $stmt = $pdo->prepare("INSERT INTO installments (account_id, installment_number, amount, due_date, status) 
                                   VALUES (?, ?, ?, ?, 'pendiente')");
            $stmt->execute([$accountId, $i, $installmentAmount, $dueDate->format('Y-m-d')]);
            $dueDate->add(new DateInterval('P30D')); // Añadir 30 días para la próxima cuota
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Cuenta de cliente creada exitosamente.', 'account_id' => $accountId];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al crear la cuenta de cliente: ' . $e->getMessage()];
    }
}
?>