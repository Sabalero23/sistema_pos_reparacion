<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/pos_functions.php';

function debug_log($message) {
    error_log("[DEBUG POS] " . $message);
}

debug_log("Iniciando página POS");
debug_log("SESSION: " . print_r($_SESSION, true));

if (!isLoggedIn()) {
    debug_log("Usuario no autenticado. Redirigiendo a login.php");
    header('Location: ' . url('login.php'));
    exit;
}

debug_log("Usuario autenticado. ID: " . $_SESSION['user_id']);

$posFunctions = new POSFunctions($pdo);

$currentCashRegisterSession = $posFunctions->getCurrentCashRegisterSession();
if (!$currentCashRegisterSession) {
    $_SESSION['flash_message'] = "No hay una sesión de caja abierta. Por favor, abra la caja antes de realizar una venta.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('cash_register.php?action=open'));
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .list-group-item {
    background-color: transparent;
    color: #1c1a1acc;
    border: none;
    padding: 0.50rem 1rem;
    transition: all 0.3s ease;
}
</style>
<div class="container my-5">
    <div class="row">
        <div class="col-md-8 order-md-1">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Detalles de la Venta</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer_search" class="form-label">Cliente</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente">
                            <button class="btn btn-outline-secondary" type="button" id="clear-customer">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="hidden" id="customer_id" name="customer_id">
                    </div>
                    
                     <button type="button" id="addProduct" class="btn btn-secondary mb-3">
                        <i class="fas fa-plus me-2"></i>Añadir Producto
                    </button>

                    <div id="productList">
                        <div class="product-item mb-3">
                            <div class="row align-items-center">
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control product-search" placeholder="Buscar producto" required>
                                        <button class="btn btn-outline-secondary clear-product" type="button">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" class="product-id" name="items[0][product_id]" required>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control quantity" name="items[0][quantity]" placeholder="Cant." required min="1">
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" step="0.01" class="form-control price" name="items[0][price]" placeholder="Precio" required>
                                </div>
                                <div class="col-sm-3 text-end">
                                    <span class="subtotal me-2">0.00</span>
                                    <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
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
                    </div>

                    <button type="button" id="processSale" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-cash-register me-2"></i>Procesar Venta
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4 order-md-2 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Resumen de Venta</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Subtotal</span>
                            <strong id="subtotal">0.00</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                <input type="checkbox" id="apply_taxes" name="apply_taxes">
                                <label for="apply_taxes">Aplicar Impuestos (21%)</label>
                            </div>
                            <strong id="taxes">0.00</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span class="fw-bold">Total</span>
                            <strong class="fw-bold" id="total">0.00</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    var userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
</script>
<script src="<?php echo url('js/pos.js'); ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>