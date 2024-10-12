<?php
header('Content-Type: application/json');

// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener los datos enviados en la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['producto_id'])) {
    $producto_id = intval($data['producto_id']);

    // Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar el producto al carrito o incrementar su cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;
    }

    // Calcular el total de items en el carrito
    $total_items = array_sum($_SESSION['carrito']);

    // Devolver una respuesta de éxito
    echo json_encode([
        'success' => true,
        'message' => 'Producto agregado al carrito',
        'total_items' => $total_items
    ]);
} else {
    // Devolver un error si no se proporcionó un ID de producto
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó un ID de producto válido'
    ]);
}