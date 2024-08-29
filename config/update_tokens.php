<?php
require_once 'config.php'; // Asegúrate de que este archivo tenga la configuración de tu base de datos

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT id FROM budgets WHERE view_token IS NULL OR view_token = ''");
$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($budgets as $budget) {
    $view_token = bin2hex(random_bytes(32));
    $updateStmt = $pdo->prepare("UPDATE budgets SET view_token = ? WHERE id = ?");
    $updateStmt->execute([$view_token, $budget['id']]);
}

echo "Tokens actualizados para " . count($budgets) . " presupuestos.";
?>