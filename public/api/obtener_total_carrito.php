<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Establecer encabezados para prevenir caché y especificar tipo de contenido
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Función para calcular el total de items en el carrito
function calcularTotalItems() {
    if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
        return 0;
    }
    return array_sum($_SESSION['carrito']);
}

// Manejar la solicitud
try {
    // Verificar si la solicitud es mediante POST (opcional, depende de tu implementación)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método de solicitud no válido');
    }

    // Calcular el total de items
    $total_items = calcularTotalItems();

    // Preparar la respuesta
    $respuesta = [
        'success' => true,
        'total_items' => $total_items
    ];

    // Enviar la respuesta
    echo json_encode($respuesta);

} catch (Exception $e) {
    // Manejar cualquier error
    $respuesta = [
        'success' => false,
        'error' => 'Error al obtener el total del carrito: ' . $e->getMessage()
    ];
    echo json_encode($respuesta);
}

// Asegurar que no haya más salida después de este punto
exit;