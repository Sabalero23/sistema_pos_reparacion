<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/user_functions.php';

// Iniciar sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado y tiene permiso para gestionar usuarios
if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('users_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$userId = $_GET['id'] ?? null;

// Manejar la acción de eliminación
if ($action === 'delete' && $userId) {
    // Verificar si el usuario tiene permiso para eliminar
    if (!hasPermission('users_delete')) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar usuarios']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = deleteUser($userId);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }
}

// Procesar otras acciones antes de incluir el header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            $result = addUser($_POST);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('users.php'));
                exit;
            } else {
                $error = $result['message'];
            }
            break;
        case 'edit':
            if ($userId) {
                $result = updateUser($userId, $_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('users.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            break;
        case 'delete':
    if (!hasPermission('users_delete')) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar usuarios']);
        exit;
    }
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $result = deleteUser($userId);
        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }
    break;
    }
}

$pageTitle = "Gestión de Usuarios";
require_once __DIR__ . '/../includes/header.php';

// Renderizar vistas
switch ($action) {
    case 'list':
        $users = getAllUsers();
        include __DIR__ . '/../views/users/list.php';
        break;
    case 'add':
        $roles = getAllRoles();
        include __DIR__ . '/../views/users/add.php';
        break;
    case 'edit':
        if (!$userId) {
            header('Location: ' . url('users.php'));
            exit;
        }
        $user = getUserById($userId);
        $roles = getAllRoles();
        include __DIR__ . '/../views/users/edit.php';
        break;
    default:
        header('Location: ' . url('users.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>