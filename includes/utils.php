<?php
// includes/utils.php

// Asegurarse de que la sesión esté iniciada
function ensureSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Función para actualizar las configuraciones del sistema
function updateSettings($data) {
    global $pdo;

    $allowedSettings = [
        'app_name', 'timezone', 'currency', 'admin_email', 
        'items_per_page', 'tax_rate', 'logo_path'
    ];

    $updateValues = [];
    $params = [];

    foreach ($allowedSettings as $setting) {
        if (isset($data[$setting])) {
            $updateValues[] = "$setting = :$setting";
            $params[":$setting"] = $data[$setting];
        }
    }

    if (!empty($updateValues)) {
        $sql = "UPDATE settings SET " . implode(', ', $updateValues);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    return getSettings();
}

// Función para manejar la carga de archivos
function handleFileUpload($file, $allowedExtensions, $uploadDir) {
    if ($file['error'] == 0) {
        $filename = $file['name'];
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFilename = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFilename;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return '/uploads/' . $newFilename;
            }
        }
    }
    return false;
}

// Función para eliminar archivos
function deleteFile($filePath) {
    $fullPath = ROOT_PATH . '/public' . $filePath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
        return true;
    }
    return false;
}

// Función para sanitizar entradas
function sanitizeInput($input) {
    if (is_array($input)) {
        foreach($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
    } else {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    return $input;
}

// Función para validar CSRF token
function validateCSRFToken($token) {
    ensureSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para generar CSRF token
function generateCSRFToken() {
    ensureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para establecer mensaje flash
function setFlashMessage($message, $type = 'info') {
    ensureSession();
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

// Función para obtener y limpiar mensaje flash
function getFlashMessage() {
    ensureSession();
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Función para redireccionar
function redirect($url) {
    header("Location: " . url($url));
    exit();
}

// Función para obtener la información de la empresa
function getCompanyInfo() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM company_info LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para actualizar la información de la empresa
function updateCompanyInfo($data) {
    global $pdo;

    $allowedFields = [
        'name', 'address', 'phone', 'email', 'website',
        'logo_path', 'legal_info', 'receipt_footer'
    ];

    $updateValues = [];
    $params = [];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateValues[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (!empty($updateValues)) {
        $sql = "UPDATE company_info SET " . implode(', ', $updateValues);
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    return false;
}
?>