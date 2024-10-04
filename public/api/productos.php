<?php
header('Content-Type: application/json');
header('Cache-Control: public, max-age=300'); // Cache for 5 minutes

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/tienda_functions.php';
require_once __DIR__ . '/../../includes/utils.php';

try {
    $response = obtenerProductos();
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error en productos.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error del servidor',
        'message' => 'Ha ocurrido un error al procesar su solicitud.'
    ]);
}

function obtenerProductos() {
    $categoria_id = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);
    $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1;
    $productos_por_pagina = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 12;

    // Generar clave de caché
    $cache_key = md5("productos_{$categoria_id}_{$pagina}");
    
    // Intentar obtener del caché
    $cached_response = obtenerCache($cache_key);
    if ($cached_response !== false) {
        return $cached_response;
    }

    // Si no está en caché, obtener de la base de datos
    $productos = getProductos($categoria_id, $pagina, $productos_por_pagina);
    $total_productos = contarProductos($categoria_id);

    $response = [
        'productos' => $productos,
        'total_productos' => $total_productos,
        'pagina_actual' => $pagina,
        'productos_por_pagina' => $productos_por_pagina,
        'total_paginas' => ceil($total_productos / $productos_por_pagina)
    ];

    // Guardar en caché
    guardarCache($cache_key, $response, 300); // 5 minutos

    return $response;
}

function obtenerCache($key) {
    if (function_exists('apcu_fetch')) {
        return apcu_fetch($key);
    }
    return false;
}

function guardarCache($key, $data, $ttl) {
    if (function_exists('apcu_store')) {
        apcu_store($key, $data, $ttl);
    }
}