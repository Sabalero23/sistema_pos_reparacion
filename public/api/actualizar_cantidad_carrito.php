<?php
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__DIR__, 2) . '/config/config.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['producto_id']) || !isset($data['action'])) {
        throw new Exception('Datos incompletos');
    }

    $producto_id = intval($data['producto_id']);
    $action = $data['action'];

    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (!isset($_SESSION['carrito'][$producto_id]) || !is_array($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] = ['cantidad' => 0];
    }

    if ($action === 'increase') {
        $_SESSION['carrito'][$producto_id]['cantidad']++;
    } elseif ($action === 'decrease' && $_SESSION['carrito'][$producto_id]['cantidad'] > 1) {
        $_SESSION['carrito'][$producto_id]['cantidad']--;
    }

    $nueva_cantidad = $_SESSION['carrito'][$producto_id]['cantidad'];

    echo json_encode([
        'success' => true,
        'message' => 'Cantidad actualizada',
        'nueva_cantidad' => $nueva_cantidad
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al actualizar la cantidad: ' . $e->getMessage()
    ]);
}