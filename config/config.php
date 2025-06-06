<?php
// config/config.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Configuración de la sesión
ini_set('session.gc_maxlifetime', 14400); // 4 horas en segundos
ini_set('session.cookie_lifetime', 14400); // 4 horas en segundos
session_set_cookie_params(14400, '/', null, true, true); // Secure y HttpOnly

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_name');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de rutas
define('BASE_URL', 'https://taller.cellcomweb.com.ar/public');
define('ROOT_PATH', dirname(__DIR__));

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar la conexión PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-03:00';"
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Función para obtener las configuraciones del sistema
function getSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM settings");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener las configuraciones del sistema
$settings = getSettings();

// Definir constantes de configuración
define('APP_NAME', $settings['app_name'] ?? 'Sistema POS');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', $settings['timezone'] ?? 'America/Argentina/Buenos_Aires');
define('CURRENCY', $settings['currency'] ?? 'ARS');
define('ADMIN_EMAIL', $settings['admin_email'] ?? 'admin@example.com');
define('ITEMS_PER_PAGE', $settings['items_per_page'] ?? 20);
define('TAX_RATE', $settings['tax_rate'] ?? 0.21);
define('LOGO_PATH', $settings['logo_path'] ?? '/uploads/logo.png');

// Configuración de zona horaria
date_default_timezone_set(TIMEZONE);

// Función para generar URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Función para verificar y renovar la sesión
function checkAndRenewSession() {
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } else if (time() - $_SESSION['CREATED'] > 14400) {
        // Si han pasado más de 4 horas, destruimos la sesión
        session_unset();
        session_destroy();
        return false;
    }
    return true;
}

// Llamar a esta función al inicio de cada script que use sesiones
checkAndRenewSession();

// Incluir el archivo de utilidades
require_once ROOT_PATH . '/includes/utils.php';

// Definir INSTALLATION_COMPLETED
define('INSTALLATION_COMPLETED', true);

// No añadir nada después de esta línea
