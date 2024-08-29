<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function authenticateUser($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function login($email, $password) {
    $user = authenticateUser($email, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role_id'];
        return true;
    }
    return false;
}

function logout() {
    $_SESSION = array();
    session_destroy();
    header("Location: " . url('login.php'));
    exit();
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . url('login.php'));
        exit;
    }
}
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>