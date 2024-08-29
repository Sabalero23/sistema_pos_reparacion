<?php
require_once __DIR__ . '/../config/config.php';

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductStock($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    return $stmt->fetchColumn();
}

function adjustStock($productId, $quantityDifference, $reason, $userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + :quantity WHERE id = :id");
        $stmt->execute([':quantity' => $quantityDifference, ':id' => $productId]);

        $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, quantity, movement_type, notes, user_id) 
                               VALUES (:product_id, :quantity, 'ajuste', :notes, :user_id)");
        $stmt->execute([
            ':product_id' => $productId,
            ':quantity' => $quantityDifference,
            ':notes' => $reason,
            ':user_id' => $userId
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Error en adjustStock: " . $e->getMessage());
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

function createInventoryAdjustment($adjustmentData, $items) {
    global $pdo;
    
    error_log("Iniciando createInventoryAdjustment");
    
    try {
        while ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("Se encontró una transacción activa y se hizo rollback");
        }
        
        $pdo->beginTransaction();
        error_log("Transacción iniciada");

        $stmt = $pdo->prepare("INSERT INTO inventory_adjustments (user_id, notes) VALUES (:user_id, :notes)");
        $stmt->execute([
            ':user_id' => $adjustmentData['user_id'],
            ':notes' => $adjustmentData['notes']
        ]);
        $adjustmentId = $pdo->lastInsertId();
        error_log("Ajuste de inventario creado con ID: " . $adjustmentId);

        $validReasons = ['dañado', 'perdido', 'correccion', 'otro'];

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $newQuantity = $item['new_quantity'];
            $reason = $item['reason'];

            if (!in_array($reason, $validReasons)) {
                throw new Exception("Razón inválida para el producto ID: " . $productId);
            }

            $currentStock = getProductStock($productId);
            error_log("Stock actual para producto ID {$productId}: {$currentStock}");

            $stmt = $pdo->prepare("INSERT INTO inventory_adjustment_items 
                                   (adjustment_id, product_id, quantity_before, quantity_after, reason) 
                                   VALUES (:adjustment_id, :product_id, :quantity_before, :quantity_after, :reason)");
            $stmt->execute([
                ':adjustment_id' => $adjustmentId,
                ':product_id' => $productId,
                ':quantity_before' => $currentStock,
                ':quantity_after' => $newQuantity,
                ':reason' => $reason
            ]);
            error_log("Item de ajuste insertado para producto ID: " . $productId);

            $quantityDifference = $newQuantity - $currentStock;
            if (!adjustStock($productId, $quantityDifference, $reason, $adjustmentData['user_id'])) {
                throw new Exception("Error al ajustar el stock del producto con ID: " . $productId);
            }
            error_log("Stock ajustado para producto ID {$productId}. Nueva cantidad: {$newQuantity}");
        }

        $pdo->commit();
        error_log("Transacción completada con éxito");
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            error_log("Se hizo rollback de la transacción debido a una excepción");
        }
        error_log("Error al crear el ajuste de inventario: " . $e->getMessage());
        throw $e;
    }
}

function handleInventoryAdjustment($adjustmentData, $items) {
    try {
        $result = createInventoryAdjustment($adjustmentData, $items);
        return [
            'success' => true,
            'message' => "Ajuste de inventario realizado con éxito."
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Error al realizar el ajuste de inventario: " . $e->getMessage()
        ];
    }
}
?>