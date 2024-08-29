<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/promotion_functions.php';

session_start();

if (!isLoggedIn() || !hasPermission('promotions_edit')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$promotionId = $_GET['id'] ?? null;

if (!$promotionId) {
    header('Location: ' . url('promotions.php'));
    exit;
}

$promotion = getPromotionById($promotionId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updatePromotion($promotionId, $_POST);
    if ($result['success']) {
        $_SESSION['flash_message'] = $result['message'];
        $_SESSION['flash_type'] = 'success';
        header('Location: ' . url('promotions.php'));
        exit;
    } else {
        $error = $result['message'];
    }
}

$products = getAllProducts();

$pageTitle = "Editar Promoción";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Editar Promoción</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="<?php echo url('promotions.php?action=edit&id=' . $promotion['id']); ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($promotion['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($promotion['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="discount_type" class="form-label">Tipo de Descuento</label>
            <select class="form-select" id="discount_type" name="discount_type" required>
                <option value="percentage" <?php if ($promotion['discount_type'] === 'percentage') echo 'selected'; ?>>Porcentaje</option>
                <option value="fixed" <?php if ($promotion['discount_type'] === 'fixed') echo 'selected'; ?>>Valor Fijo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="discount_value" class="form-label">Valor de Descuento</label>
            <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" value="<?php echo $promotion['discount_value']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de Inicio</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $promotion['start_date']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de Fin</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $promotion['end_date']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="product_id" class="form-label">Producto</label>
            <select class="form-select" id="product_id" name="product_id">
                <option value="">Selecciona un producto (opcional)</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>" <?php if ($promotion['product_id'] == $product['id']) echo 'selected'; ?>><?php echo htmlspecialchars($product['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="<?php echo url('promotions.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>