<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/sale_functions.php';

if (!isLoggedIn() || !hasPermission('sales_edit')) {
    $_SESSION['flash_message'] = "No tienes permiso para editar ventas.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('sales.php'));
    exit;
}

$saleId = $_GET['id'] ?? null;

if (!$saleId) {
    $_SESSION['flash_message'] = "ID de venta no proporcionado.";
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . url('sales.php'));
    exit;
}

$saleFunctions = new SaleFunctions($pdo);
$sale = $saleFunctions->getSaleById($saleId);
$saleItems = $saleFunctions->getSaleItems($saleId);

if (!$sale) {
    $_SESSION['flash_message'] = "Venta no encontrada.";
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . url('sales.php'));
    exit;
}

$pageTitle = "Editar Venta #" . $saleId;
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="editSaleForm" action="<?php echo url('sales.php?action=edit&id=' . $saleId); ?>" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="sale_id" value="<?php echo $saleId; ?>">
        
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="customer_search" value="<?php echo htmlspecialchars($sale['customer_name'] ?? ''); ?>" required>
            <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $sale['customer_id']; ?>" required>
        </div>
        
        <div id="productList">
            <?php foreach ($saleItems as $index => $item): ?>
                <div class="product-item mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control product-search" value="<?php echo htmlspecialchars($item['product_name']); ?>" required>
                            <input type="hidden" class="product-id" name="items[<?php echo $index; ?>][product_id]" value="<?php echo $item['product_id']; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control quantity" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>" required min="1">
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control price" name="items[<?php echo $index; ?>][price]" value="<?php echo $item['price']; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <span class="form-control-plaintext subtotal"><?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="addProduct" class="btn btn-secondary mb-3">Añadir Producto</button>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo $sale['total_amount']; ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Método de Pago</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <?php
                $paymentMethods = ['efectivo', 'tarjeta', 'transferencia', 'credito', 'otros'];
                foreach ($paymentMethods as $method) {
                    $selected = ($method === $sale['payment_method']) ? 'selected' : '';
                    echo "<option value=\"$method\" $selected>" . ucfirst($method) . "</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
        <a href="<?php echo url('sales.php?action=view&id=' . $saleId); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script src="<?php echo url('js/sales.js'); ?>"></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>