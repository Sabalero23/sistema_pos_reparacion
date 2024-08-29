<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$term = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$context = filter_input(INPUT_GET, 'context', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (empty($term) || empty($type)) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($type === 'customer') {
        $stmt = $pdo->prepare("SELECT id, name FROM customers WHERE name LIKE :term LIMIT 20");
    } elseif ($type === 'supplier') {
        $stmt = $pdo->prepare("SELECT id, name FROM suppliers WHERE name LIKE :term LIMIT 20");
    } elseif ($type === 'product') {
        if ($context === 'purchase') {
            $stmt = $pdo->prepare("SELECT id, name, cost_price AS price, stock_quantity FROM products WHERE name LIKE :term OR description LIKE :term OR sku LIKE :term LIMIT 20");
        } else {
            $stmt = $pdo->prepare("SELECT id, name, price, stock_quantity FROM products WHERE name LIKE :term OR description LIKE :term OR sku LIKE :term LIMIT 20");
        }
    } else {
        echo json_encode([]);
        exit;
    }

    $stmt->execute(['term' => "%$term%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log the results for debugging
    error_log('Search context: ' . $context);
    error_log('Search results: ' . print_r($results, true));

    echo json_encode($results);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}