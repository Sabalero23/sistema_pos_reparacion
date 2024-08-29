<?php
require_once __DIR__ . '/../config/config.php';

function getAllHomeVisits() {
    global $pdo;
    
    $query = "SELECT hv.*, c.name as customer_name 
              FROM home_visits hv 
              JOIN customers c ON hv.customer_id = c.id 
              ORDER BY hv.visit_date, hv.visit_time";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createHomeVisit($data) {
    global $pdo;
    try {
        $sql = "INSERT INTO home_visits (customer_id, visit_date, visit_time, notes) 
                VALUES (:customer_id, :visit_date, :visit_time, :notes)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':customer_id' => $data['customer_id'],
            ':visit_date' => $data['visit_date'],
            ':visit_time' => $data['visit_time'],
            ':notes' => $data['notes'] ?? null
        ]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating home visit: " . $e->getMessage());
        throw new Exception("Error al crear la visita a domicilio");
    }
}

function getHomeVisit($id) {
    global $pdo;
    try {
        $sql = "SELECT hv.*, c.name as customer_name, c.address, c.phone 
                FROM home_visits hv 
                JOIN customers c ON hv.customer_id = c.id 
                WHERE hv.id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting home visit: " . $e->getMessage());
        throw new Exception("Error al obtener la visita a domicilio");
    }
}

function updateHomeVisit($id, $data) {
    global $pdo;
    try {
        $sql = "UPDATE home_visits 
                SET customer_id = :customer_id, 
                    visit_date = :visit_date, 
                    visit_time = :visit_time, 
                    notes = :notes 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':customer_id' => $data['customer_id'],
            ':visit_date' => $data['visit_date'],
            ':visit_time' => $data['visit_time'],
            ':notes' => $data['notes'] ?? null
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Error updating home visit: " . $e->getMessage());
        throw new Exception("Error al actualizar la visita a domicilio");
    }
}

function deleteHomeVisit($id) {
    global $pdo;
    try {
        $sql = "DELETE FROM home_visits WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        error_log("Error deleting home visit: " . $e->getMessage());
        throw new Exception("Error al eliminar la visita a domicilio");
    }
}

function updateHomeVisitStatus($id, $status) {
    global $pdo;
    try {
        error_log("Intentando actualizar el estado de la visita ID: $id a: $status");
        
        // Primero, verificamos si la visita existe
        $checkSql = "SELECT id FROM home_visits WHERE id = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        
        if ($checkStmt->rowCount() === 0) {
            error_log("No se encontró la visita con ID: $id");
            throw new Exception("No se encontró la visita con el ID: $id");
        }
        
        $sql = "UPDATE home_visits SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
        
        if (!$result) {
            error_log("Error al ejecutar la consulta: " . print_r($stmt->errorInfo(), true));
            throw new Exception("No se pudo actualizar el estado de la visita.");
        }
        
        if ($stmt->rowCount() === 0) {
            error_log("No se actualizó ninguna fila para la visita con ID: $id");
            throw new Exception("No se pudo actualizar el estado de la visita.");
        }
        
        error_log("Estado de la visita actualizado con éxito. ID: $id, Nuevo estado: $status");
        return true;
    } catch (PDOException $e) {
        error_log("Error PDO al actualizar el estado de la visita: " . $e->getMessage());
        throw new Exception("Error al actualizar el estado de la visita a domicilio: " . $e->getMessage());
    }
}