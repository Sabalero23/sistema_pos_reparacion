<?php
require_once dirname(__DIR__, 2) . '/config/config.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['producto_id'])) {
        throw new Exception('ID de producto no proporcionado');
    }

    $producto_id = intval($data['producto_id']);

    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado del carrito'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al eliminar el producto: ' . $e->getMessage()
    ]);
}