<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/pos_functions.php';

header('Content-Type: application/json');

$posFunctions = new POSFunctions($pdo);

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input)) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos de venta.']);
    exit;
}

// Iniciar la sesión si aún no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Asignar el ID de usuario de la sesión
$input['user_id'] = $_SESSION['user_id'] ?? null;

$result = $posFunctions->createSale($input);
echo json_encode($result);
?>