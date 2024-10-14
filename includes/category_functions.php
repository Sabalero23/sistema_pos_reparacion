<?php
function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addCategory($data) {
    global $pdo;
    $errors = validateCategoryData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, active_in_store) VALUES (?, ?, ?)");
        $active_in_store = isset($data['active_in_store']) ? 1 : 0;
        $stmt->execute([$data['name'], $data['description'], $active_in_store]);
        return ['success' => true, 'message' => 'Categoría añadida exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al añadir categoría: ' . $e->getMessage()];
    }
}

function updateCategory($id, $data) {
    global $pdo;
    $errors = validateCategoryData($data);
    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('<br>', $errors)];
    }

    try {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, active_in_store = ? WHERE id = ?");
        $active_in_store = isset($data['active_in_store']) ? 1 : 0;
        $stmt->execute([$data['name'], $data['description'], $active_in_store, $id]);
        return ['success' => true, 'message' => 'Categoría actualizada exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar categoría: ' . $e->getMessage()];
    }
}

function deleteCategory($id) {
    global $pdo;
    try {
        // Primero, verificar si hay productos asociados a esta categoría
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar la categoría porque tiene productos asociados.'];
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true, 'message' => 'Categoría eliminada exitosamente.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar categoría: ' . $e->getMessage()];
    }
}

function validateCategoryData($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = "El nombre de la categoría es requerido.";
    }

    if (strlen($data['name']) > 50) {
        $errors[] = "El nombre de la categoría no debe exceder los 50 caracteres.";
    }

    return $errors;
}

function importCategoriesFromCSV($categories) {
    $successCount = 0;
    $errorCount = 0;
    $errorMessages = [];

    foreach ($categories as $category) {
        try {
            $name = $category['name'];
            $description = $category['description'];
            $active_in_store = isset($category['active_in_store']) ? $category['active_in_store'] : 1;

            $categoryId = insertOrUpdateCategory($name, $description, $active_in_store);

            $successCount++;
        } catch (\Exception $e) {
            $errorCount++;
            $errorMessages[] = "Error al procesar la categoría '{$category['name']}': " . $e->getMessage();
        }
    }

    $result = [
        'success' => $errorCount === 0,
        'message' => "Se importaron {$successCount} categorías exitosamente. {$errorCount} errores encontrados.",
        'errors' => $errorMessages
    ];

    return $result;
}

function insertOrUpdateCategory($name, $description, $active_in_store) {
    global $pdo;

    // Verificar si la categoría existe por el nombre
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        // Actualizar la categoría existente
        $stmt = $pdo->prepare("UPDATE categories SET description = ?, active_in_store = ? WHERE id = ?");
        $stmt->execute([$description, $active_in_store, $category['id']]);
        return $category['id'];
    } else {
        // Insertar una nueva categoría
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, active_in_store) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $active_in_store]);
        return $pdo->lastInsertId();
    }
}

function getActiveCategoriesForStore() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories WHERE active_in_store = 1 ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>