<?php
// Desactivar la salida de errores
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Iniciar buffer de salida
ob_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/tienda_functions.php';

header('Content-Type: application/json');

try {
    // Verificar si la sesión está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Obtener y decodificar los datos del cliente
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar los datos del cliente');
    }

    // Verificar si hay productos en el carrito
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        throw new Exception('El carrito está vacío');
    }

    // Procesar el pedido
    $pedido = [
        'cliente' => $input,
        'productos' => [],
        'total' => 0
    ];

    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        $producto = obtenerProductoPorId($producto_id);
        if ($producto) {
            $subtotal = $producto['price'] * $cantidad;
            $pedido['productos'][] = [
                'id' => $producto_id,
                'name' => $producto['name'],
                'cantidad' => $cantidad,
                'precio' => $producto['price'],
                'subtotal' => $subtotal
            ];
            $pedido['total'] += $subtotal;
        }
    }

    // Aquí deberías guardar el pedido en la base de datos
    // Por ahora, solo simularemos que se ha guardado

    // Vaciar el carrito
    $_SESSION['carrito'] = [];

    echo json_encode([
        'success' => true,
        'message' => 'Pedido procesado con éxito',
        'pedido' => $pedido
    ]);

} catch (Exception $e) {
    // Log del error
    error_log('Error en procesar_pedido.php: ' . $e->getMessage());
    
    // Respuesta de error
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el pedido: ' . $e->getMessage()
    ]);
}

// Limpiar y finalizar el buffer de salida
ob_end_flush();