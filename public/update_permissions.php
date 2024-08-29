<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la lista de permisos definidos
    $definedPermissions = require __DIR__ . '/../config/permissions.php';

    // Insertar o actualizar permisos en la base de datos
    $stmt = $pdo->prepare("INSERT INTO permissions (name, description) VALUES (?, ?) ON DUPLICATE KEY UPDATE description = VALUES(description)");
    
    foreach ($definedPermissions as $name => $description) {
        $stmt->execute([$name, $description]);
    }

    // Asignar nuevos permisos al rol de administrador
    $adminRoleId = $pdo->query("SELECT id FROM roles WHERE name = 'admin'")->fetchColumn();
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO role_permissions (role_id, permission_id)
        SELECT ?, id FROM permissions WHERE name = ?
    ");

    foreach ($definedPermissions as $name => $description) {
        $stmt->execute([$adminRoleId, $name]);
    }

    echo "Permisos actualizados correctamente.";
} catch (PDOException $e) {
    die("Error al actualizar permisos: " . $e->getMessage());
}
?>