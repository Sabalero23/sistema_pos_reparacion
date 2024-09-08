<?php
function generateAccessToken() {
    return bin2hex(random_bytes(16)); // Genera un token de 32 caracteres
}

function getAllHomeVisits() {
    global $pdo;
    $stmt = $pdo->query("SELECT hv.*, c.name as customer_name, c.phone as customer_phone, hv.access_token 
                         FROM home_visits hv 
                         JOIN customers c ON hv.customer_id = c.id 
                         ORDER BY hv.visit_date DESC, hv.visit_time DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHomeVisit($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT hv.*, c.name as customer_name, c.phone as customer_phone, hv.access_token 
                           FROM home_visits hv 
                           JOIN customers c ON hv.customer_id = c.id 
                           WHERE hv.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createHomeVisit($data) {
    global $pdo;
    try {
        $accessToken = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO home_visits (customer_id, visit_date, visit_time, notes, status, access_token) 
                               VALUES (?, ?, ?, ?, 'programada', ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['visit_date'],
            $data['visit_time'],
            $data['notes'] ?? null,
            $accessToken
        ]);
        return ['success' => true, 'access_token' => $accessToken];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear la visita: ' . $e->getMessage()];
    }
}

function updateHomeVisit($id, $data) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE home_visits 
                               SET customer_id = ?, visit_date = ?, visit_time = ?, notes = ?, status = ? 
                               WHERE id = ?");
        $stmt->execute([
            $data['customer_id'],
            $data['visit_date'],
            $data['visit_time'],
            $data['notes'] ?? null,
            $data['status'],
            $id
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar la visita: ' . $e->getMessage()];
    }
}

function deleteHomeVisit($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM home_visits WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar la visita: ' . $e->getMessage()];
    }
}

function getHomeVisitByToken($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT hv.*, c.name as customer_name, c.phone as customer_phone 
                           FROM home_visits hv 
                           JOIN customers c ON hv.customer_id = c.id 
                           WHERE hv.access_token = ?");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}