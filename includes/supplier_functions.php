<?php
function getAllSuppliers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSupplierById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addSupplier($data) {
    global $pdo;
    $errors = validateSupplierData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact_person, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address']
        ]);
        return ['success' => true, 'message' => 'Proveedor añadido exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al añadir proveedor: ' . $e->getMessage()];
    }
}

function updateSupplier($id, $data) {
    global $pdo;
    $errors = validateSupplierData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact_person = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $id
        ]);
        return ['success' => true, 'message' => 'Proveedor actualizado exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar proveedor: ' . $e->getMessage()];
    }
}

function deleteSupplier($id) {
    global $pdo;
    try {
        // Primero, verificar si hay productos asociados a este proveedor
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE supplier_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar el proveedor porque tiene productos asociados.'];
        }

        $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Proveedor eliminado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'No se encontró el proveedor para eliminar.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar proveedor: ' . $e->getMessage()];
    }
}

function validateSupplierData($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = "El nombre del proveedor es requerido.";
    }

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El email no es válido.";
    }

    if (!empty($data['phone']) && !preg_match("/^[0-9]{10}$/", $data['phone'])) {
        $errors[] = "El número de teléfono debe tener 10 dígitos.";
    }

    return $errors;
}

function importSuppliersFromCSV($suppliers) {
    global $pdo;
    $successCount = 0;
    $errorCount = 0;
    $errorMessages = [];

    foreach ($suppliers as $supplier) {
        try {
            $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact_person, email, phone, address) 
                                   VALUES (?, ?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   contact_person = VALUES(contact_person), 
                                   email = VALUES(email), 
                                   phone = VALUES(phone), 
                                   address = VALUES(address)");
            $stmt->execute([
                $supplier['name'],
                $supplier['contact_person'],
                $supplier['email'],
                $supplier['phone'],
                $supplier['address']
            ]);
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
            $errorMessages[] = "Error al procesar el proveedor '{$supplier['name']}': " . $e->getMessage();
        }
    }

    $result = [
        'success' => $errorCount === 0,
        'message' => "Se importaron {$successCount} proveedores exitosamente. {$errorCount} errores encontrados.",
        'errors' => $errorMessages
    ];

    return $result;
}
?>