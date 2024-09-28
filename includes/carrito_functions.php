<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Agrega un producto al carrito de compras
 * 
 * @param int $producto_id
 * @param int $cantidad
 */
function agregarAlCarrito($producto_id, $cantidad = 1) {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] += $cantidad;
    } else {
        $_SESSION['carrito'][$producto_id] = $cantidad;
    }
}

/**
 * Elimina un producto del carrito de compras
 * 
 * @param int $producto_id
 */
function eliminarDelCarrito($producto_id) {
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
    }
}

/**
 * Obtiene el contenido del carrito de compras
 * 
 * @return array
 */
function obtenerCarrito() {
    global $pdo;
    
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return [];
    }
    
    $ids = array_keys($_SESSION['carrito']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productos as &$producto) {
        $producto['cantidad'] = $_SESSION['carrito'][$producto['id']];
        $producto['subtotal'] = $producto['price'] * $producto['cantidad'];
    }
    
    return $productos;
}

/**
 * Calcula el total del carrito de compras
 * 
 * @return float
 */
function calcularTotalCarrito() {
    $carrito = obtenerCarrito();
    return array_sum(array_column($carrito, 'subtotal'));
}

/**
 * Obtiene los productos en el carrito con detalles
 * 
 * @return array
 */
function obtenerProductosEnCarrito() {
    $carrito = obtenerCarrito();
    $productos = [];
    
    foreach ($carrito as $producto) {
        $productos[] = [
            'id' => $producto['id'],
            'nombre' => $producto['name'],
            'precio' => $producto['price'],
            'cantidad' => $producto['cantidad'],
            'subtotal' => $producto['subtotal']
        ];
    }
    
    return $productos;
}