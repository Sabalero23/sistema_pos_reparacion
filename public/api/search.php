<?php
require_once __DIR__ . '/../../config/config.php';

// Configuración de errores para desarrollo
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Configuración de headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Access-Control-Allow-Origin: *');

// Función para logging
function debug_log($message) {
    error_log("[DEBUG SEARCH] " . $message);
}

// Obtener y sanitizar parámetros
$term = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

debug_log("Búsqueda iniciada - Término: $term, Tipo: $type");

if (empty($term) || empty($type)) {
    echo json_encode(['error' => 'Parámetros de búsqueda incompletos']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = '';
    $params = ['term' => "%$term%"];

    switch ($type) {
        case 'customer':
            $query = "SELECT id, name FROM customers WHERE name LIKE :term LIMIT 20";
            break;
        case 'product':
            $query = "SELECT id, name, price, stock_quantity FROM products WHERE name LIKE :term OR description LIKE :term OR sku LIKE :term LIMIT 20";
            break;
        case 'supplier':
            $query = "SELECT id, name FROM suppliers WHERE name LIKE :term LIMIT 20";
            break;
        default:
            echo json_encode(['error' => 'Tipo de búsqueda no válido']);
            exit;
    }

    debug_log("Query: $query");

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    debug_log("Resultados encontrados: " . count($results));

    echo json_encode($results);
} catch (PDOException $e) {
    debug_log("Error de PDO: " . $e->getMessage());
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    debug_log("Error general: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}