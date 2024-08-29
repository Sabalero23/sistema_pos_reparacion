<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/stock_functions.php';

function getAllPurchases() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, s.name as supplier_name, u.name as user_name 
                         FROM purchases p 
                         LEFT JOIN suppliers s ON p.supplier_id = s.id 
                         LEFT JOIN users u ON p.user_id = u.id 
                         ORDER BY p.purchase_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPurchaseById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, s.name as supplier_name, u.name as user_name 
                           FROM purchases p 
                           LEFT JOIN suppliers s ON p.supplier_id = s.id 
                           LEFT JOIN users u ON p.user_id = u.id 
                           WHERE p.id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPurchaseItems($purchaseId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pi.*, p.name as product_name 
                           FROM purchase_items pi 
                           JOIN products p ON pi.product_id = p.id 
                           WHERE pi.purchase_id = :purchase_id");
    $stmt->execute([':purchase_id' => $purchaseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createPurchase($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO purchases (supplier_id, user_id, total_amount, status) 
                               VALUES (:supplier_id, :user_id, :total_amount, 'pendiente')");
        $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':user_id' => $_SESSION['user_id'],
            ':total_amount' => $data['total_amount']
        ]);
        $purchaseId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, price) 
                               VALUES (:purchase_id, :product_id, :quantity, :price)");
        foreach ($data['items'] as $item) {
            $stmt->execute([
                ':purchase_id' => $purchaseId,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        // Registrar el movimiento de creación
        registerPurchaseMovement($purchaseId, 'creacion', 'Compra creada');

        $pdo->commit();
        return ['success' => true, 'message' => 'Compra creada exitosamente.'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'message' => 'Error al crear la compra: ' . $e->getMessage()];
    }
}

function receivePurchase($purchaseId, $data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $purchase = getPurchaseById($purchaseId);
        if (!$purchase) {
            throw new Exception("Compra no encontrada.");
        }

        if ($purchase['status'] !== 'pendiente') {
            throw new Exception("La compra ya ha sido recibida o cancelada.");
        }

        $stmt = $pdo->prepare("UPDATE purchases SET status = 'recibido', received_date = NOW(), total_amount = :total_amount WHERE id = :id");
        
        $totalAmount = 0;
        $stmt2 = $pdo->prepare("UPDATE purchase_items SET received_quantity = :received_quantity, price = :price WHERE id = :item_id");
        foreach ($data['items'] as $itemId => $itemData) {
            $receivedQuantity = $itemData['received_quantity'];
            $price = $itemData['price'];
            $subtotal = $receivedQuantity * $price;
            $totalAmount += $subtotal;

            $stmt2->execute([
                ':item_id' => $itemId,
                ':received_quantity' => $receivedQuantity,
                ':price' => $price
            ]);

            $item = getPurchaseItemById($itemId);
            updateStock($item['product_id'], $receivedQuantity, 'compra', $purchaseId, 'Recepción de compra');
        }

        $stmt->execute([
            ':id' => $purchaseId,
            ':total_amount' => $totalAmount
        ]);

        // Registrar el movimiento de la compra
        addPurchaseMovement($purchaseId, $_SESSION['user_id'], 'recepcion', "Compra recibida. Total actualizado: $" . number_format($totalAmount, 2));

        $pdo->commit();
        return ['success' => true, 'message' => 'Compra recibida exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al recibir la compra: ' . $e->getMessage()];
    }
}

function cancelPurchase($id) {
    global $pdo;
    try {
        $purchase = getPurchaseById($id);
        if (!$purchase) {
            return ['success' => false, 'message' => 'La compra no existe.'];
        }
        if ($purchase['status'] === 'cancelado') {
            return ['success' => false, 'message' => 'La compra ya ha sido cancelada.'];
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE purchases SET status = 'cancelado' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($purchase['status'] === 'recibido') {
            $purchaseItems = getPurchaseItems($id);
            foreach ($purchaseItems as $item) {
                $updateResult = updateStock($item['product_id'], -$item['received_quantity'], 'cancelacion_compra', $id, 'Cancelación de compra');
                if (!$updateResult) {
                    throw new Exception("Error al actualizar el stock del producto: " . $item['product_id']);
                }
            }
        }

        // Registrar el movimiento de cancelación
        registerPurchaseMovement($id, 'cancelacion', 'Compra cancelada');

        $pdo->commit();
        return ['success' => true, 'message' => 'Compra cancelada exitosamente.'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'message' => 'Error al cancelar la compra: ' . $e->getMessage()];
    }
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name, cost_price FROM products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllSuppliers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registerPurchaseMovement($purchaseId, $movementType, $details) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO purchase_movements (purchase_id, user_id, movement_type, details) 
                           VALUES (:purchase_id, :user_id, :movement_type, :details)");
    $stmt->execute([
        ':purchase_id' => $purchaseId,
        ':user_id' => $_SESSION['user_id'],
        ':movement_type' => $movementType,
        ':details' => $details
    ]);
}

function getPurchaseMovements($purchaseId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pm.*, u.name as user_name 
                           FROM purchase_movements pm
                           JOIN users u ON pm.user_id = u.id
                           WHERE pm.purchase_id = :purchase_id
                           ORDER BY pm.created_at DESC");
    $stmt->execute([':purchase_id' => $purchaseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>