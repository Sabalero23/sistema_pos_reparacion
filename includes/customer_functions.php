<?php
function getAllCustomers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addCustomer($data) {
    global $pdo;
    error_log("Datos recibidos en addCustomer: " . json_encode($data));
    $errors = validateCustomerData($data);
    if (!empty($errors)) {
        error_log("Errores de validación: " . json_encode($errors));
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address']
        ]);
        error_log("Resultado de la inserción: " . ($result ? "éxito" : "fallo"));
        if ($result) {
            return ['success' => true, 'message' => 'Cliente añadido exitosamente.'];
        } else {
            error_log("Error al insertar: " . json_encode($stmt->errorInfo()));
            return ['success' => false, 'message' => 'Error al añadir cliente: ' . implode(", ", $stmt->errorInfo())];
        }
    } catch (PDOException $e) {
        error_log("Excepción PDO: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al añadir cliente: ' . $e->getMessage()];
    }
}

function updateCustomer($id, $data) {
    global $pdo;
    $errors = validateCustomerData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $id
        ]);
        return ['success' => true, 'message' => 'Cliente actualizado exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar cliente: ' . $e->getMessage()];
    }
}

function deleteCustomer($id) {
    global $pdo;
    try {
        // Verificar si hay ventas asociadas a este cliente
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE customer_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar el cliente porque tiene ventas asociadas.'];
        }

        // Si no hay ventas, proceder con la eliminación
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Cliente eliminado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'No se encontró el cliente para eliminar.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar cliente: ' . $e->getMessage()];
    }
}

function validateCustomerData($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = "El nombre del cliente es requerido.";
    }

    if (!empty($data['email'])) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido.";
        }
    }

    if (!empty($data['phone']) && !preg_match("/^[0-9]{1,12}$/", $data['phone'])) {
        $errors[] = "El número de teléfono debe tener hasta 12 dígitos numéricos.";
    }

    return $errors;
}

function getCustomerAccountSummary($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT balance FROM customer_accounts WHERE customer_id = ?");
    $stmt->execute([$customerId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_sales FROM sales WHERE customer_id = ? AND is_credit = 1");
    $stmt->execute([$customerId]);
    $sales = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT SUM(amount) as total_payments FROM payments WHERE customer_id = ?");
    $stmt->execute([$customerId]);
    $payments = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'balance' => $account['balance'] ?? 0,
        'total_sales' => $sales['total_sales'] ?? 0,
        'total_payments' => $payments['total_payments'] ?? 0
    ];
}

function getCustomerTransactions($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 'venta' as type, sale_date as date, total_amount as amount, balance 
                           FROM sales 
                           WHERE customer_id = ? AND is_credit = 1
                           UNION ALL
                           SELECT 'pago' as type, payment_date as date, amount, 0 as balance 
                           FROM payments 
                           WHERE customer_id = ?
                           ORDER BY date DESC");
    $stmt->execute([$customerId, $customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>