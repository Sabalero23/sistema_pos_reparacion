<?php
$pageTitle = $pageTitle ?? "Nueva Venta";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sale_functions.php';

$saleFunctions = new SaleFunctions($pdo);

// Verificar si hay una sesión de caja abierta
$currentCashRegisterSession = $saleFunctions->getCurrentCashRegisterSession();
if (!$currentCashRegisterSession) {
    echo '<div class="container mt-4">';
    echo '<div class="alert alert-warning">No hay una sesión de caja abierta. Por favor, abra una caja antes de realizar una venta.</div>';
    echo '<a href="' . url('cash_register.php?action=open') . '" class="btn btn-primary">Abrir Caja</a>';
    echo '</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Nueva Venta</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="saleForm" action="<?php echo url('sales.php?action=create'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente" required>
            <input type="hidden" id="customer_id" name="customer_id" required>
            <div class="invalid-feedback">
                Por favor seleccione un cliente.
            </div>
        </div>
        
        <button type="button" id="addProduct" class="btn btn-secondary mb-3">Añadir Producto</button>

        <div id="productList">
    <div class="product-item mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control product-search" placeholder="Buscar producto" required>
                <input type="hidden" class="product-id" name="items[0][product_id]" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity" name="items[0][quantity]" placeholder="Cantidad" required min="1">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control price" name="items[0][price]" placeholder="Precio" required>
            </div>
            <div class="col-md-2">
                <span class="form-control-plaintext subtotal">0.00</span>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
</div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Método de Pago</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
                <option value="transferencia">Transferencia</option>
                <option value="credito">Crédito</option>
                <option value="otros">Otros</option>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un método de pago.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Crear Venta</button>
        <a href="<?php echo url('sales.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1000 !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script src="<?php echo url('js/sales.js'); ?>"></script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>