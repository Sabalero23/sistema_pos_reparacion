<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


if (!isLoggedIn() || !hasPermission('roles_manage')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$roleId = $_GET['id'] ?? null;

function getAllPermissions() {
    global $pdo;
    $stmt = $pdo->query("SELECT `id`, `name` AS `key`, `description` FROM `permissions`");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRolePermissions($roleId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.`id`, p.`name` AS `key`, p.`description` 
                           FROM `permissions` p
                           INNER JOIN `role_permissions` rp ON p.`id` = rp.`permission_id`
                           WHERE rp.`role_id` = ?");
    $stmt->execute([$roleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateRolePermissions($roleId, $permissionKeys) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // Eliminar los permisos existentes del rol
        $stmt = $pdo->prepare("DELETE FROM `role_permissions` WHERE `role_id` = ?");
        $stmt->execute([$roleId]);
        
        // Obtener los IDs de los permisos seleccionados
        $placeholders = implode(',', array_fill(0, count($permissionKeys), '?'));
        $stmt = $pdo->prepare("SELECT `id` FROM `permissions` WHERE `name` IN ($placeholders)");
        $stmt->execute($permissionKeys);
        $permissionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Agregar los nuevos permisos seleccionados
        $stmt = $pdo->prepare("INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES (?, ?)");
        foreach ($permissionIds as $permissionId) {
            $stmt->execute([$roleId, $permissionId]);
        }
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Permisos actualizados exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al actualizar los permisos: ' . $e->getMessage()];
    }
}

$pageTitle = "Gestión de Roles y Permisos";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        $roles = getAllRoles();
        include __DIR__ . '/../views/roles/list.php';
        break;
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = addRole($_POST['name']);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('roles.php'));
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include __DIR__ . '/../views/roles/add.php';
        break;
    case 'edit':
        $role = getRoleById($roleId);
        if ($role === false) {
            $_SESSION['flash_message'] = "El rol no existe.";
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . url('roles.php'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = updateRole($roleId, $_POST['name']);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('roles.php'));
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include __DIR__ . '/../views/roles/edit.php';
        break;
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = deleteRole($roleId);
            echo json_encode($result);
            exit;
        }
        break;
    case 'permissions':
        $role = getRoleById($roleId);
        if ($role === false) {
            $_SESSION['flash_message'] = "El rol no existe.";
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . url('roles.php'));
            exit;
        }
        $allPermissions = getAllPermissions();
        $rolePermissions = getRolePermissions($roleId);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = updateRolePermissions($roleId, $_POST['permissions'] ?? []);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('roles.php'));
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include __DIR__ . '/../views/roles/permissions.php';
        break;
    default:
        header('Location: ' . url('roles.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>