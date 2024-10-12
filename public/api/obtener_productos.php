<?php
// Habilitar el reporte de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir los archivos necesarios
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/tienda_functions.php';
require_once __DIR__ . '/../includes/db.php';

// Configurar cabeceras
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Función para registrar errores
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . "Error en obtener_productos.php: " . $message . PHP_EOL, 3, __DIR__ . '/../logs/error.log');
}

try {
    // Validar y obtener parámetros
    $categoria_id = isset($_GET['categoria']) ? filter_var($_GET['categoria'], FILTER_VALIDATE_INT) : null;
    $pagina = isset($_GET['pagina']) ? max(1, filter_var($_GET['pagina'], FILTER_VALIDATE_INT)) : 1;
    $productos_por_pagina = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 20;

    // Registrar los parámetros recibidos
    logError("Parámetros recibidos - Categoría: " . ($categoria_id ?? 'null') . ", Página: $pagina, Productos por página: $productos_por_pagina");

    // Verificar la conexión a la base de datos
    if (!$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Obtener productos
    $productos = getProductos($categoria_id, $pagina, $productos_por_pagina);

    // Verificar si se obtuvieron productos
    if ($productos === false) {
        throw new Exception("Error al obtener productos de la base de datos");
    }

    logError("Productos obtenidos: " . count($productos));

    // Enviar respuesta
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'pagina_actual' => $pagina,
        'productos_por_pagina' => $productos_por_pagina
    ]);

} catch (Exception $e) {
    logError($e->getMessage() . "\nTraza: " . $e->getTraceAsString());
    
    // Enviar respuesta de error
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener productos. Por favor, contacte al administrador.',
        'debug_message' => $e->getMessage() // Remover esta línea en producción
    ]);
}

// Cerrar la conexión a la base de datos si está abierta
if (isset($conn) && $conn) {
    $conn->close();
}