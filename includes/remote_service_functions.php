<?php
require_once __DIR__ . '/../config/config.php';

function getAllRemoteServices() {
    global $pdo;
    $stmt = $pdo->query("SELECT rs.*, c.name as customer_name, c.phone as customer_phone, rs.access_token, rs.service_date as scheduled_date  
                         FROM remote_services rs 
                         JOIN customers c ON rs.customer_id = c.id 
                         ORDER BY rs.service_date DESC, rs.service_time DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRemoteService($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT rs.*, c.name as customer_name, c.phone as customer_phone, rs.access_token 
                           FROM remote_services rs 
                           JOIN customers c ON rs.customer_id = c.id 
                           WHERE rs.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createRemoteService($data) {
    global $pdo;
    try {
        $accessToken = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO remote_services (customer_id, service_date, service_time, notes, status, access_token) 
                               VALUES (?, ?, ?, ?, 'programado', ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['service_date'],
            $data['service_time'],
            $data['notes'] ?? null,
            $accessToken
        ]);
        return ['success' => true, 'access_token' => $accessToken];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear el servicio remoto: ' . $e->getMessage()];
    }
}

function updateRemoteService($id, $data) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE remote_services 
                               SET customer_id = ?, service_date = ?, service_time = ?, notes = ?, status = ? 
                               WHERE id = ?");
        $stmt->execute([
            $data['customer_id'],
            $data['service_date'],
            $data['service_time'],
            $data['notes'] ?? null,
            $data['status'],
            $id
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar el servicio remoto: ' . $e->getMessage()];
    }
}

function deleteRemoteService($id) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Primero, verificamos si el servicio remoto existe
        $stmt = $pdo->prepare("SELECT id FROM remote_services WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception("El servicio remoto no existe.");
        }

        // Si existe, procedemos a eliminarlo
        $stmt = $pdo->prepare("DELETE FROM remote_services WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            $pdo->commit();
            return ['success' => true];
        } else {
            throw new Exception("No se pudo eliminar el servicio remoto.");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getRemoteServiceByToken($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT rs.*, c.name as customer_name, c.phone as customer_phone 
                           FROM remote_services rs 
                           JOIN customers c ON rs.customer_id = c.id 
                           WHERE rs.access_token = ?");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getScheduledRemoteServices() {
    global $pdo;
    $query = "SELECT rs.id, rs.service_date, rs.service_time, c.name as customer_name
              FROM remote_services rs
              JOIN customers c ON rs.customer_id = c.id
              WHERE rs.status = 'programado'
              ORDER BY rs.service_date ASC, rs.service_time ASC
              LIMIT 5";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}