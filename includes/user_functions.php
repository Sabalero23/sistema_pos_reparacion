<?php
require_once __DIR__ . '/../config/config.php';

function getUserById($userId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    // Remove sensitive information
    unset($user['password']);

    return $user;
}

function getUserProfile($userId) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserProfile($userId, $name, $email, $bio, $location, $website) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        // Update user information
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $userId]);

        // Update or insert user profile
        $userProfile = getUserProfile($userId);
        if ($userProfile) {
            $stmt = $pdo->prepare("UPDATE user_profiles SET bio = ?, location = ?, website = ? WHERE user_id = ?");
            $stmt->execute([$bio, $location, $website, $userId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, bio, location, website) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $bio, $location, $website]);
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getAllUsers() {
    global $pdo;

    $stmt = $pdo->query("SELECT u.id, u.name, u.email, u.role_id, r.name as role_name 
                         FROM users u 
                         LEFT JOIN roles r ON u.role_id = r.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addUser($userData) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $stmt->execute([$userData['name'], $userData['email'], $hashedPassword, $userData['role_id']]);

        $userId = $pdo->lastInsertId();

        $pdo->commit();
        return ['success' => true, 'message' => 'Usuario añadido exitosamente'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al añadir usuario: ' . $e->getMessage()];
    }
}

function updateUser($userId, $userData) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
        $stmt->execute([$userData['name'], $userData['email'], $userData['role_id'], $userId]);

        if (!empty($userData['password'])) {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt->execute([$hashedPassword, $userId]);
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()];
    }
}

function deleteUser($userId) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            return ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
        } else {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'No se encontró el usuario para eliminar'];
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al eliminar usuario: ' . $e->getMessage()];
    }
}