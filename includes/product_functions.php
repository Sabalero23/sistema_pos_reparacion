<?php
function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, c.name as category_name, s.name as supplier_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN suppliers s ON p.supplier_id = s.id 
                         ORDER BY p.name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addProduct($data, $image) {
    global $pdo;
    $errors = validateProductData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    $imagePath = null;
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $imagePath = uploadProductImage($image);
        if (!$imagePath) {
            return ['success' => false, 'message' => 'Error al subir la imagen del producto.'];
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, sku, category_id, price, cost_price, stock_quantity, reorder_level, supplier_id, image_path, active_in_store) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['sku'],
            $data['category_id'],
            $data['price'],
            $data['cost_price'],
            $data['stock_quantity'],
            $data['reorder_level'],
            $data['supplier_id'],
            $imagePath,
            isset($data['active_in_store']) ? 1 : 0
        ]);
        return ['success' => true, 'message' => 'Producto añadido exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al añadir producto: ' . $e->getMessage()];
    }
}

function updateProduct($id, $data, $image) {
    global $pdo;
    $errors = validateProductData($data, false);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    $currentProduct = getProductById($id);
    $imagePath = $currentProduct['image_path'];

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $newImagePath = uploadProductImage($image);
        if ($newImagePath) {
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            $imagePath = $newImagePath;
        } else {
            return ['success' => false, 'message' => 'Error al subir la nueva imagen del producto.'];
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE products SET 
                               name = ?, 
                               description = ?, 
                               sku = ?, 
                               category_id = ?, 
                               price = ?, 
                               cost_price = ?, 
                               stock_quantity = ?, 
                               reorder_level = ?, 
                               supplier_id = ?,
                               image_path = ?,
                               active_in_store = ?
                               WHERE id = ?");
        $result = $stmt->execute([
            $data['name'],
            $data['description'],
            $data['sku'],
            $data['category_id'],
            $data['price'],
            $data['cost_price'],
            $data['stock_quantity'],
            $data['reorder_level'],
            $data['supplier_id'],
            $imagePath,
            isset($data['active_in_store']) ? 1 : 0,
            $id
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Producto actualizado exitosamente.'];
        } else {
            return ['success' => false, 'message' => 'No se pudo actualizar el producto.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()];
    }
}

function deleteProduct($id) {
    global $pdo;
    try {
        $product = getProductById($id);
        if ($product['image_path'] && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }

        // Verificar si el producto está siendo utilizado en otras tablas
        $tables = ['budget_items', 'sale_items', 'purchase_items', 'reservation_items'];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE product_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => "No se puede eliminar el producto porque está siendo utilizado en $table."];
            }
        }

        // Si no está siendo utilizado, proceder con la eliminación
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Producto eliminado exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()];
    }
}

function validateProductData($data, $isNew = true) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = "El nombre del producto es requerido.";
    }

    if (empty($data['sku'])) {
        $errors[] = "El SKU es requerido.";
    }

    if (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = "El precio debe ser un número positivo.";
    }

    if (!is_numeric($data['cost_price']) || $data['cost_price'] < 0) {
        $errors[] = "El precio de costo debe ser un número positivo.";
    }

    if (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
        $errors[] = "La cantidad en stock debe ser un número positivo.";
    }

    if (!is_numeric($data['reorder_level']) || $data['reorder_level'] < 0) {
        $errors[] = "El nivel de reorden debe ser un número positivo.";
    }

    return $errors;
}

function uploadProductImage($image) {
    $targetDir = "uploads/products/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($image["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Permitir ciertos formatos de archivo
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        // Subir archivo al servidor
        if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
            return $targetFilePath;
        }
    }

    return false;
}

function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllSuppliers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function importProductsFromCSV($products) {
    $successCount = 0;
    $errorCount = 0;
    $errorMessages = [];

    foreach ($products as $product) {
        try {
            // Validar y procesar los datos del producto
            $sku = $product['sku'];
            $name = $product['name'];
            $description = $product['description'];
            $categoryId = (int)$product['category_id'];
            $price = (float)$product['price'];
            $costPrice = (float)$product['cost_price'];
            $stockQuantity = (int)$product['stock_quantity'];
            $minStock = (int)$product['min_stock'];
            $maxStock = (int)$product['max_stock'];
            $reorderLevel = (int)$product['reorder_level'];
            $supplierId = (int)$product['supplier_id'];

            echo "Procesando producto con SKU: $sku\n";

            // Verificar si la categoría y el proveedor existen
            $category = getCategoryById($categoryId);
            $supplier = getSupplierById($supplierId);

            if (!$category) {
                throw new Exception("La categoría con ID $categoryId no existe.");
            }

            if (!$supplier) {
                throw new Exception("El proveedor con ID $supplierId no existe.");
            }

            // Insertar o actualizar el producto en la base de datos
            $productId = insertOrUpdateProduct($sku, $name, $description, $categoryId, $price, $costPrice, $stockQuantity, $minStock, $maxStock, $reorderLevel, $supplierId);

            $successCount++;
        } catch (\Exception $e) {
            $errorCount++;
            $errorMessages[] = "Error al procesar el producto con SKU '{$product['sku']}': " . $e->getMessage();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    $result = [
        'success' => $errorCount === 0,
        'message' => "Se importaron {$successCount} productos exitosamente. {$errorCount} errores encontrados.",
        'errors' => $errorMessages
    ];

    return $result;
}

function insertOrUpdateProduct($sku, $name, $description, $categoryId, $price, $costPrice, $stockQuantity, $minStock, $maxStock, $reorderLevel, $supplierId) {
    global $pdo;

    // Verificar si el producto existe por el SKU
    $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->execute([$sku]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Actualizar el producto existente
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, category_id = ?, price = ?, cost_price = ?, stock_quantity = ?, min_stock = ?, max_stock = ?, reorder_level = ?, supplier_id = ? WHERE id = ?");
        $stmt->execute([$name, $description, $categoryId, $price, $costPrice, $stockQuantity, $minStock, $maxStock, $reorderLevel, $supplierId, $product['id']]);
        return $product['id'];
    } else {
        // Insertar un nuevo producto
        $stmt = $pdo->prepare("INSERT INTO products (sku, name, description, category_id, price, cost_price, stock_quantity, min_stock, max_stock, reorder_level, supplier_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$sku, $name, $description, $categoryId, $price, $costPrice, $stockQuantity, $minStock, $maxStock, $reorderLevel, $supplierId]);
        return $pdo->lastInsertId();
    }
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSupplierById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}