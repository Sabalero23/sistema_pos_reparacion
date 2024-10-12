<?php
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__DIR__, 2) . '/config/config.php';

header('Content-Type: application/json');

try {
    if (!isset($pdo)) {
        throw new Exception("La conexión a la base de datos no está disponible");
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $carrito = $_SESSION['carrito'];
    $totalItems = 0;

    $productosDetallados = [];
    foreach ($carrito as $productoId => $item) {
        $stmt = $pdo->prepare("SELECT id, name, price, image_path FROM products WHERE id = ?");
        $stmt->execute([$productoId]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Asegurarse de que la cantidad sea un número entero positivo
            $cantidad = isset($item['cantidad']) ? max(1, intval($item['cantidad'])) : 1;
            $producto['cantidad'] = $cantidad;
            $producto['subtotal'] = floatval($producto['price']) * $cantidad;
            $productosDetallados[] = $producto;
            $totalItems += $cantidad;
        }
    }

    echo json_encode([
        'success' => true,
        'productos' => $productosDetallados,
        'total_items' => $totalItems
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener el carrito: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}