<?php
require_once __DIR__ . '/../config/config.php';

function getCategorias() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductos($categoria_id = null, $pagina = 1, $productos_por_pagina = 12) {
    global $pdo;
    
    $offset = ($pagina - 1) * $productos_por_pagina;
    $params = [];
    $where_clause = "WHERE active_in_store = 1";
    
    if ($categoria_id !== null) {
        $where_clause .= " AND category_id = :categoria_id";
        $params[':categoria_id'] = $categoria_id;
    }
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            $where_clause 
            ORDER BY p.name 
            LIMIT :offset, :limit";
    
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $productos_por_pagina, PDO::PARAM_INT);
    
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productos as &$producto) {
        $producto['image_path'] = getImagenProducto($producto['image_path']);
    }
    
    return $productos;
}

function contarProductos($categoria_id = null) {
    global $pdo;
    
    $where_clause = "WHERE active_in_store = 1";
    $params = [];
    
    if ($categoria_id !== null) {
        $where_clause .= " AND category_id = :categoria_id";
        $params[':categoria_id'] = $categoria_id;
    }
    
    $sql = "SELECT COUNT(*) FROM products $where_clause";
    
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    return $stmt->fetchColumn();
}

function buscarProductos($termino_busqueda, $pagina = 1, $productos_por_pagina = 12) {
    global $pdo;
    
    $offset = ($pagina - 1) * $productos_por_pagina;
    $termino_busqueda = '%' . strtolower($termino_busqueda) . '%';
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.active_in_store = 1 
            AND LOWER(p.name) LIKE :termino 
            ORDER BY p.name 
            LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':termino', $termino_busqueda, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $productos_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productos as &$producto) {
        $producto['image_path'] = getImagenProducto($producto['image_path']);
    }
    
    return $productos;
}

function formatearPrecio($precio) {
    return '$ ' . number_format($precio, 2, ',', '.');
}

function getImagenProducto($image_path) {
    if (!empty($image_path)) {
        $image_path = '/' . ltrim($image_path, '/');
        return url($image_path);
    }
    return url('assets/img/producto-sin-imagen.png');
}

function generarPaginacion($pagina_actual, $total_paginas, $url_base) {
    $html = '<nav aria-label="Paginación de productos"><ul class="pagination">';
    
    if ($pagina_actual > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . ($pagina_actual - 1) . '">Anterior</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }
    
    for ($i = max(1, $pagina_actual - 2); $i <= min($total_paginas, $pagina_actual + 2); $i++) {
        if ($i == $pagina_actual) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($pagina_actual < $total_paginas) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . ($pagina_actual + 1) . '">Siguiente</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

function contarProductosBusqueda($termino_busqueda) {
    global $pdo;
    
    $termino_busqueda = '%' . strtolower($termino_busqueda) . '%';
    
    $sql = "SELECT COUNT(*) FROM products 
            WHERE active_in_store = 1 
            AND LOWER(name) LIKE :termino";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':termino', $termino_busqueda, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn();
}