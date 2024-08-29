<?php
$pageTitle = "Nueva Compra";
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/header.php';

// Verificar permisos
if (!isLoggedIn() || !hasPermission('purchases_create')) {
    $_SESSION['flash_message'] = "No tienes permiso para crear compras.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('purchases.php'));
    exit;
}

// Obtener los datos necesarios
$products = getAllProducts();
$suppliers = getAllSuppliers();

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $result = createPurchase($postData);
    if ($result['success']) {
        $_SESSION['flash_message'] = $result['message'];
        $_SESSION['flash_type'] = 'success';
        header('Location: ' . url('purchases.php'));
        exit;
    } else {
        $error = $result['message'];
    }
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Nueva Compra</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="purchaseForm" action="<?php echo url('purchases.php?action=create'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="supplier_search" class="form-label">Proveedor</label>
            <input type="text" class="form-control" id="supplier_search" placeholder="Buscar proveedor" required>
            <input type="hidden" id="supplier_id" name="supplier_id" required>
            <div class="invalid-feedback">Por favor, seleccione un proveedor.</div>
        </div>

        <div id="productList">
            <div class="product-item mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control product-search" placeholder="Buscar producto" required>
                        <input type="hidden" class="product-id" name="items[0][product_id]" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control quantity" name="items[0][quantity]" placeholder="Cantidad" required min="1" step="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control price" name="items[0][price]" placeholder="Precio" required min="0.01">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="addProduct" class="btn btn-secondary mb-3">Añadir Producto</button>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Crear Compra</button>
        <a href="<?php echo url('purchases.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="<?php echo url('js/purchases.js'); ?>"></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>