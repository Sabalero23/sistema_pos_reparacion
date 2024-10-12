<?php
require_once __DIR__ . '/../config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Implementar caché en memoria
$cache = [];
$cache_ttl = 300; // 5 minutos

function getCacheKey($function, ...$args) {
    return $function . '_' . md5(serialize($args));
}

function getFromCache($key) {
    global $cache;
    if (isset($cache[$key]) && $cache[$key]['expires'] > time()) {
        return $cache[$key]['data'];
    }
    return null;
}

function setCache($key, $data) {
    global $cache, $cache_ttl;
    $cache[$key] = [
        'data' => $data,
        'expires' => time() + $cache_ttl
    ];
}

function getCategorias() {
    $cache_key = getCacheKey(__FUNCTION__);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        setCache($cache_key, $result);
        return $result;
    } catch (PDOException $e) {
        error_log("Error en getCategorias: " . $e->getMessage());
        return [];
    }
}

function getProductos($categoria_id = null, $pagina = 1, $productos_por_pagina = 12) {
    $cache_key = getCacheKey(__FUNCTION__, $categoria_id, $pagina, $productos_por_pagina);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $offset = ($pagina - 1) * $productos_por_pagina;
    $params = [':offset' => $offset, ':limit' => $productos_por_pagina];
    $where_clause = "WHERE p.active_in_store = 1";
    
    if ($categoria_id !== null) {
        $where_clause .= " AND p.category_id = :categoria_id";
        $params[':categoria_id'] = $categoria_id;
    }
    
    $sql = "SELECT p.id, p.name, p.price, p.image_path, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            $where_clause 
            ORDER BY p.name 
            LIMIT :offset, :limit";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($productos as &$producto) {
            $producto['image_path'] = getImagenProducto($producto['image_path']);
            $producto['price_formatted'] = formatearPrecio($producto['price']);
        }
        
        setCache($cache_key, $productos);
        return $productos;
    } catch (PDOException $e) {
        error_log("Error en getProductos: " . $e->getMessage());
        return [];
    }
}

function contarProductos($categoria_id = null) {
    $cache_key = getCacheKey(__FUNCTION__, $categoria_id);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $where_clause = "WHERE active_in_store = 1";
    $params = [];
    
    if ($categoria_id !== null) {
        $where_clause .= " AND category_id = :categoria_id";
        $params[':categoria_id'] = $categoria_id;
    }
    
    $sql = "SELECT COUNT(*) FROM products $where_clause";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetchColumn();
        
        setCache($cache_key, $result);
        return $result;
    } catch (PDOException $e) {
        error_log("Error en contarProductos: " . $e->getMessage());
        return 0;
    }
}

function buscarProductos($termino_busqueda, $pagina = 1, $productos_por_pagina = 12) {
    $cache_key = getCacheKey(__FUNCTION__, $termino_busqueda, $pagina, $productos_por_pagina);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $offset = ($pagina - 1) * $productos_por_pagina;
    $termino_busqueda = '%' . strtolower($termino_busqueda) . '%';
    
    $sql = "SELECT p.id, p.name, p.price, p.image_path, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.active_in_store = 1 
            AND (LOWER(p.name) LIKE :termino OR LOWER(p.description) LIKE :termino)
            ORDER BY 
                CASE 
                    WHEN LOWER(p.name) LIKE :termino_exacto THEN 1
                    WHEN LOWER(p.name) LIKE :termino_inicio THEN 2
                    ELSE 3
                END,
                p.name 
            LIMIT :limit OFFSET :offset";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':termino', $termino_busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':termino_exacto', strtolower($termino_busqueda), PDO::PARAM_STR);
        $stmt->bindValue(':termino_inicio', strtolower($termino_busqueda) . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $productos_por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($productos as &$producto) {
            $producto['image_path'] = getImagenProducto($producto['image_path']);
            $producto['price_formatted'] = formatearPrecio($producto['price']);
        }
        
        setCache($cache_key, $productos);
        return $productos;
    } catch (PDOException $e) {
        error_log("Error en buscarProductos: " . $e->getMessage());
        return [];
    }
}

function formatearPrecio($precio) {
    return number_format($precio, 2, ',', '.');
}

function getImagenProducto($image_path) {
    if (!empty($image_path)) {
        $image_path = '/' . ltrim($image_path, '/');
        return url($image_path);
    }
    return url('assets/img/producto-sin-imagen.png');
}

function generarPaginacion($pagina_actual, $total_paginas, $url_base) {
    if ($total_paginas <= 1) return '';
    
    $html = '<nav aria-label="Paginación de productos"><ul class="pagination">';
    
    // Botón Anterior
    $html .= $pagina_actual > 1 
        ? '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . ($pagina_actual - 1) . '" data-pagina="' . ($pagina_actual - 1) . '">Anterior</a></li>'
        : '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    
    // Números de página
    $rango = 2;
    $bloques = [
        [$pagina_actual - $rango, $pagina_actual + $rango],
        [1, 1 + $rango],
        [$total_paginas - $rango, $total_paginas]
    ];
    
    $mostrados = [];
    foreach ($bloques as $bloque) {
        [$inicio, $fin] = $bloque;
        $inicio = max(1, $inicio);
        $fin = min($total_paginas, $fin);
        
        for ($i = $inicio; $i <= $fin; $i++) {
            if (!in_array($i, $mostrados)) {
                if ($mostrados && end($mostrados) != $i - 1) {
                    $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                $html .= $i == $pagina_actual
                    ? '<li class="page-item active"><span class="page-link">' . $i . '</span></li>'
                    : '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . $i . '" data-pagina="' . $i . '">' . $i . '</a></li>';
                $mostrados[] = $i;
            }
        }
    }
    
    // Botón Siguiente
    $html .= $pagina_actual < $total_paginas 
        ? '<li class="page-item"><a class="page-link" href="' . $url_base . '&pagina=' . ($pagina_actual + 1) . '" data-pagina="' . ($pagina_actual + 1) . '">Siguiente</a></li>'
        : '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    
    $html .= '</ul></nav>';
    return $html;
}

function contarProductosBusqueda($termino_busqueda) {
    $cache_key = getCacheKey(__FUNCTION__, $termino_busqueda);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $termino_busqueda = '%' . strtolower($termino_busqueda) . '%';
    
    $sql = "SELECT COUNT(*) FROM products 
            WHERE active_in_store = 1 
            AND (LOWER(name) LIKE :termino OR LOWER(description) LIKE :termino)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':termino', $termino_busqueda, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetchColumn();
        setCache($cache_key, $result);
        return $result;
    } catch (PDOException $e) {
        error_log("Error en contarProductosBusqueda: " . $e->getMessage());
        return 0;
    }
}

function getProductoDetalle($producto_id) {
    $cache_key = getCacheKey(__FUNCTION__, $producto_id);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id AND p.active_in_store = 1";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $producto_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            $producto['image_path'] = getImagenProducto($producto['image_path']);
            $producto['price_formatted'] = formatearPrecio($producto['price']);
        }
        
        setCache($cache_key, $producto);
        return $producto;
    } catch (PDOException $e) {
        error_log("Error en getProductoDetalle: " . $e->getMessage());
        return null;
    }
}

function getProductosRelacionados($producto_id, $categoria_id, $limit = 4) {
    $cache_key = getCacheKey(__FUNCTION__, $producto_id, $categoria_id, $limit);
    $cached = getFromCache($cache_key);
    if ($cached !== null) return $cached;

    global $pdo;
    
    $sql = "SELECT p.id, p.name, p.price, p.image_path 
            FROM products p
            WHERE p.active_in_store = 1 
            AND p.category_id = :categoria_id 
            AND p.id != :producto_id
            ORDER BY RAND()
            LIMIT :limit";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindValue(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($productos as &$producto) {
            $producto['image_path'] = getImagenProducto($producto['image_path']);
            $producto['price_formatted'] = formatearPrecio($producto['price']);
        }
        
        setCache($cache_key, $productos);
        return $productos;
    } catch (PDOException $e) {
        error_log("Error en getProductosRelacionados: " . $e->getMessage());
        return [];
    }
}

function limpiarCache() {
    global $cache;
    $cache = [];
}

function handleApiRequest($action) {
    try {
        error_log("Iniciando handleApiRequest con acción: " . $action);
        
        switch ($action) {
            case 'get_productos':
                $categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : null;
                $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                $productos_por_pagina = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
                
                error_log("Parámetros: categoria_id=$categoria_id, pagina=$pagina, productos_por_pagina=$productos_por_pagina");
                
                $productos = getProductos($categoria_id, $pagina, $productos_por_pagina);
                error_log("Productos obtenidos: " . print_r($productos, true));
                
                $total_productos = contarProductos($categoria_id);
                error_log("Total productos: $total_productos");
                
                $total_paginas = ceil($total_productos / $productos_por_pagina);
                error_log("Total páginas: $total_paginas");
                
                $categoria_nombre = $categoria_id ? getCategoryName($categoria_id) : null;
                error_log("Nombre de categoría: " . ($categoria_nombre ?? 'null'));
                
                $response = [
                    'success' => true, 
                    'productos' => $productos, 
                    'categoria_nombre' => $categoria_nombre,
                    'total_paginas' => $total_paginas,
                    'pagina_actual' => $pagina
                ];
                
                error_log("Respuesta preparada: " . print_r($response, true));
                
                jsonResponse($response);
                break;
            
            case 'buscar_productos':
                $termino = isset($_GET['q']) ? $_GET['q'] : '';
                $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                $productos_por_pagina = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
                
                error_log("Búsqueda: término='$termino', pagina=$pagina, productos_por_pagina=$productos_por_pagina");
                
                $productos = buscarProductos($termino, $pagina, $productos_por_pagina);
                $total_productos = contarProductosBusqueda($termino);
                $total_paginas = ceil($total_productos / $productos_por_pagina);
                
                $response = [
                    'success' => true, 
                    'productos' => $productos, 
                    'total_paginas' => $total_paginas,
                    'pagina_actual' => $pagina
                ];
                
                error_log("Respuesta de búsqueda preparada: " . print_r($response, true));
                
                jsonResponse($response);
                break;
            
            case 'get_categorias':
                $categorias = getCategorias();
                jsonResponse(['success' => true, 'categorias' => $categorias]);
                break;
            
            case 'get_producto_detalle':
                $producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $producto = getProductoDetalle($producto_id);
                if ($producto) {
                    jsonResponse(['success' => true, 'producto' => $producto]);
                } else {
                    errorResponse("Producto no encontrado", 404);
                }
                break;
            
            case 'get_productos_relacionados':
                $producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;
                $productos = getProductosRelacionados($producto_id, $categoria_id, $limit);
                jsonResponse(['success' => true, 'productos' => $productos]);
                break;
            
            default:
                errorResponse("Acción inválida", 400);
        }
    } catch (Exception $e) {
        error_log("Excepción en handleApiRequest: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        errorResponse("Error del servidor: " . $e->getMessage());
    }
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    $json = json_encode($data);
    if ($json === false) {
        error_log("Error en json_encode: " . json_last_error_msg());
        echo json_encode(['success' => false, 'error' => 'Error al codificar la respuesta']);
    } else {
        error_log("Enviando respuesta JSON: " . $json);
        echo $json;
    }
    exit;
}

function errorResponse($message, $statusCode = 500) {
    error_log("Enviando respuesta de error: $message (Status: $statusCode)");
    jsonResponse(['success' => false, 'error' => $message], $statusCode);
}

function getCategoryName($categoria_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$categoria_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error en getCategoryName: " . $e->getMessage());
        return null;
    }
}

// Al final del archivo
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    error_log("Archivo llamado directamente. Acción: $action");
    handleApiRequest($action);
}

function obtenerProductoPorId($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($producto) {
            $producto['price_formatted'] = number_format($producto['price'], 2, ',', '.');
            $producto['image_path'] = getImagenProducto($producto['image_path']);
        }
        return $producto;
    } catch (PDOException $e) {
        error_log('Error en obtenerProductoPorId: ' . $e->getMessage());
        return null;
    }
}