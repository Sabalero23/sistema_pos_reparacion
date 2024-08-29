<?php
require_once __DIR__ . '/../config/config.php';

function updateStock($productId, $quantity, $movementType, $referenceId, $notes = '') {
    global $pdo;
    
    // Validar el tipo de movimiento
    $validMovementTypes = ['compra', 'venta', 'ajuste', 'devolución'];
    if (!in_array($movementType, $validMovementTypes)) {
        throw new Exception("Tipo de movimiento no válido: $movementType");
    }

    // Actualizar el stock del producto
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + :quantity WHERE id = :product_id");
    $stmt->execute([':quantity' => $quantity, ':product_id' => $productId]);

    // Registrar el movimiento de stock
    $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, quantity, movement_type, reference_id, notes, user_id) 
                           VALUES (:product_id, :quantity, :movement_type, :reference_id, :notes, :user_id)");
    $stmt->execute([
        ':product_id' => $productId,
        ':quantity' => $quantity,
        ':movement_type' => $movementType,
        ':reference_id' => $referenceId,
        ':notes' => $notes,
        ':user_id' => $_SESSION['user_id']
    ]);

    return true;
}

function getProductStock($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    return $stmt->fetchColumn();
}

function createInventoryAdjustment($adjustmentData, $items) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Insertar el ajuste de inventario
        $stmt = $pdo->prepare("INSERT INTO inventory_adjustments (user_id, notes) VALUES (:user_id, :notes)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':notes' => $adjustmentData['notes']
        ]);
        $adjustmentId = $pdo->lastInsertId();

        // Insertar los items del ajuste y actualizar el stock
        $stmt = $pdo->prepare("INSERT INTO inventory_adjustment_items 
                               (adjustment_id, product_id, quantity_before, quantity_after, reason) 
                               VALUES (:adjustment_id, :product_id, :quantity_before, :quantity_after, :reason)");
        foreach ($items as $item) {
            $currentStock = getProductStock($item['product_id']);
            $stmt->execute([
                ':adjustment_id' => $adjustmentId,
                ':product_id' => $item['product_id'],
                ':quantity_before' => $currentStock,
                ':quantity_after' => $item['new_quantity'],
                ':reason' => $item['reason']
            ]);

            $quantityDifference = $item['new_quantity'] - $currentStock;
            updateStock($item['product_id'], $quantityDifference, 'adjustment', $adjustmentId, $adjustmentData['notes']);
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al crear el ajuste de inventario: " . $e->getMessage());
        return false;
    }
}

function getLowStockProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_quantity <= min_stock ORDER BY stock_quantity ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStockMovements($productId = null, $startDate = null, $endDate = null) {
    global $pdo;
    $sql = "SELECT sm.*, p.name as product_name, u.name as user_name 
            FROM stock_movements sm
            JOIN products p ON sm.product_id = p.id
            JOIN users u ON sm.user_id = u.id
            WHERE 1=1";
    $params = [];

    if ($productId) {
        $sql .= " AND sm.product_id = :product_id";
        $params[':product_id'] = $productId;
    }
    if ($startDate) {
        $sql .= " AND sm.created_at >= :start_date";
        $params[':start_date'] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND sm.created_at <= :end_date";
        $params[':end_date'] = $endDate;
    }

    $sql .= " ORDER BY sm.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>