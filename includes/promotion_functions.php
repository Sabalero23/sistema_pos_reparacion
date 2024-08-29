<?php
function getAllPromotions() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, pr.name as product_name 
                         FROM promotions p
                         LEFT JOIN products pr ON p.product_id = pr.id
                         ORDER BY p.start_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPromotionById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, pr.name as product_name
                           FROM promotions p
                           LEFT JOIN products pr ON p.product_id = pr.id
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createPromotion($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO promotions (name, description, discount_type, discount_value, start_date, end_date, product_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $productId = $data['product_id'] !== '' ? $data['product_id'] : null;
    $result = $stmt->execute([
        $data['name'],
        $data['description'],
        $data['discount_type'],
        $data['discount_value'],
        $data['start_date'],
        $data['end_date'],
        $productId
    ]);

    if ($result) {
        return ['success' => true, 'message' => 'Promoción creada exitosamente.'];
    } else {
        return ['success' => false, 'message' => 'Error al crear la promoción.'];
    }
}

function updatePromotion($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE promotions SET name = ?, description = ?, discount_type = ?, discount_value = ?,
                           start_date = ?, end_date = ?, product_id = ? WHERE id = ?");
    $result = $stmt->execute([
        $data['name'],
        $data['description'],
        $data['discount_type'],
        $data['discount_value'],
        $data['start_date'],
        $data['end_date'],
        $data['product_id'] ?? null,
        $id
    ]);

    if ($result) {
        return ['success' => true, 'message' => 'Promoción actualizada exitosamente.'];
    } else {
        return ['success' => false, 'message' => 'Error al actualizar la promoción.'];
    }
}

function deletePromotion($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM promotions WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        return ['success' => true, 'message' => 'Promoción eliminada exitosamente.'];
    } else {
        return ['success' => false, 'message' => 'Error al eliminar la promoción.'];
    }
}
?>