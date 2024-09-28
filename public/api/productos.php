<?php
// Deshabilitar la salida de errores PHP
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para manejar errores y devolverlos como JSON
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'error' => 'Error del servidor',
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit;
}

// Establecer el manejador de errores personalizado
set_error_handler("handleError");

// Iniciar el búfer de salida
ob_start();

// Incluir los archivos necesarios
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../../includes/tienda_functions.php';

try {
    $categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $productos_por_pagina = 20;

    $productos = getProductos($categoria_id, $pagina, $productos_por_pagina);
    $total_productos = contarProductos($categoria_id);

    $response = [
        'productos' => $productos,
        'total_productos' => $total_productos,
        'pagina_actual' => $pagina,
        'productos_por_pagina' => $productos_por_pagina
    ];

    // Limpiar cualquier salida previa
    ob_clean();

    // Establecer las cabeceras y enviar la respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $e) {
    // Limpiar cualquier salida previa
    ob_clean();

    // Loguear el error
    error_log("Error en productos.php: " . $e->getMessage());

    // Enviar respuesta de error como JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor', 'message' => $e->getMessage()]);
}

// Finalizar y liberar el búfer de salida
ob_end_flush();