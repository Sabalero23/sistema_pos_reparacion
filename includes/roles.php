<?php
function hasPermission($permission) {
    global $pdo;
    $userId = getCurrentUserId();
    if (!$userId) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT 1
        FROM users u
        JOIN role_permissions rp ON u.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE u.id = ? AND p.name = ?
    ");
    $stmt->execute([$userId, $permission]);
    return $stmt->fetchColumn() !== false;
}

function getAllRoles() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM roles ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoleById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addRole($name) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
        $result = $stmt->execute([$name]);
        if ($result) {
            return ['success' => true, 'message' => 'Rol añadido exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al añadir el rol.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al añadir el rol: ' . $e->getMessage()];
    }
}

function updateRole($id, $name) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE roles SET name = ? WHERE id = ?");
    return $stmt->execute([$name, $id]);
}

function deleteRole($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    return $stmt->execute([$id]);
}
?>