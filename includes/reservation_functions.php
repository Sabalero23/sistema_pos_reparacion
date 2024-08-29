<?php
function getAllReservations() {
    global $pdo;
    $stmt = $pdo->query("SELECT r.*, c.name as customer_name, u.name as user_name 
                         FROM reservations r 
                         LEFT JOIN customers c ON r.customer_id = c.id 
                         LEFT JOIN users u ON r.user_id = u.id 
                         ORDER BY r.reservation_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getReservationById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, u.name as user_name 
                           FROM reservations r 
                           LEFT JOIN customers c ON r.customer_id = c.id 
                           LEFT JOIN users u ON r.user_id = u.id 
                           WHERE r.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getReservationItems($reservationId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT ri.*, p.name as product_name 
                           FROM reservation_items ri 
                           JOIN products p ON ri.product_id = p.id 
                           WHERE ri.reservation_id = ?");
    $stmt->execute([$reservationId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createReservation($data) {
    global $pdo;
    try {
        $errors = validateReservationData($data);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(", ", $errors)];
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO reservations (customer_id, user_id, total_amount, status, notes) 
                               VALUES (?, ?, ?, 'pendiente', ?)");
        $stmt->execute([
            $data['customer_id'],
            $_SESSION['user_id'],
            $data['total_amount'],
            $data['notes'] ?? null
        ]);
        $reservationId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO reservation_items (reservation_id, product_id, quantity, price) 
                               VALUES (?, ?, ?, ?)");
        foreach ($data['items'] as $item) {
            $stmt->execute([
                $reservationId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Reserva creada exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al crear la reserva: ' . $e->getMessage()];
    }
}

function confirmReservation($id) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT status FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $currentStatus = $stmt->fetchColumn();

        if (!$currentStatus) {
            throw new Exception("Reserva no encontrada.");
        }

        if ($currentStatus !== 'pendiente') {
            throw new Exception("Solo se pueden confirmar reservas pendientes.");
        }

        $stmt = $pdo->prepare("UPDATE reservations SET status = 'confirmado' WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("No se pudo actualizar el estado de la reserva.");
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Reserva confirmada exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al confirmar la reserva: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function convertReservationToSale($reservationId, $data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Obtener la información de la reserva
        $reservation = getReservationById($reservationId);
        if (!$reservation) {
            throw new Exception("Reserva no encontrada");
        }

        if ($reservation['status'] !== 'confirmado') {
            throw new Exception("Solo se pueden convertir reservas confirmadas");
        }

        $reservationItems = getReservationItems($reservationId);
        if (empty($reservationItems)) {
            throw new Exception("No se encontraron items para la reserva");
        }

        // Validar el método de pago
        if (empty($data['payment_method']) || !in_array($data['payment_method'], ['efectivo', 'tarjeta', 'transferencia', 'otros'])) {
            throw new Exception("Método de pago no válido");
        }

        // Crear la venta
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, user_id, total_amount, payment_method, status) 
                               VALUES (?, ?, ?, ?, 'completado')");
        $stmt->execute([
            $reservation['customer_id'],
            $_SESSION['user_id'],
            $reservation['total_amount'],
            $data['payment_method']
        ]);
        $saleId = $pdo->lastInsertId();

        // Insertar los items de la venta
        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) 
                               VALUES (?, ?, ?, ?)");
        foreach ($reservationItems as $item) {
            $stmt->execute([
                $saleId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // Actualizar el stock del producto
            updateProductStock($item['product_id'], -$item['quantity']);

            // Registrar el movimiento de stock
            registerStockMovement($item['product_id'], -$item['quantity'], 'venta', $saleId, 'Venta de producto');
        }

        // Actualizar el estado de la reserva
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'convertido' WHERE id = ?");
        $stmt->execute([$reservationId]);

        $pdo->commit();
        return ['success' => true, 'message' => 'Reserva convertida en venta exitosamente.', 'sale_id' => $saleId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al convertir la reserva en venta: ' . $e->getMessage()];
    }
}

function updateProductStock($productId, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
    $stmt->execute([$quantity, $productId]);
}

function registerStockMovement($productId, $quantity, $movementType, $referenceId, $notes) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, quantity, movement_type, reference_id, notes, user_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$productId, $quantity, $movementType, $referenceId, $notes, $_SESSION['user_id']]);
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCustomers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validateReservationData($data) {
    $errors = [];

    if (empty($data['customer_id'])) {
        $errors[] = "El cliente es obligatorio";
    }

    if (empty($data['items']) || !is_array($data['items'])) {
        $errors[] = "Debe incluir al menos un producto";
    } else {
        foreach ($data['items'] as $index => $item) {
            if (empty($item['product_id'])) {
                $errors[] = "Falta el ID del producto en el ítem " . ($index + 1);
            }
            if (empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                $errors[] = "Cantidad inválida en el ítem " . ($index + 1);
            }
            if (empty($item['price']) || !is_numeric($item['price']) || $item['price'] <= 0) {
                $errors[] = "Precio inválido en el ítem " . ($index + 1);
            }
        }
    }

    if (empty($data['total_amount']) || !is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
        $errors[] = "El total es inválido";
    }

    return $errors;
}
?>