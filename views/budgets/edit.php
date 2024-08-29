<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1>Editar Presupuesto</h1>
    
    <form action="<?php echo url('budget.php?action=edit&id=' . $budget['id']); ?>" method="post" id="budgetForm">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="mb-3">
            <label for="customer_id" class="form-label">Cliente</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>" <?php echo ($customer['id'] == $budget['customer_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="productList">
            <?php foreach ($budget['items'] as $index => $item): ?>
                <div class="product-item mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="items[<?php echo $index; ?>][product_id]" class="form-select product-select" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" 
                                            data-price="<?php echo $product['price']; ?>"
                                            <?php echo ($product['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[<?php echo $index; ?>][quantity]" class="form-control quantity" placeholder="Cantidad" required min="1" value="<?php echo $item['quantity']; ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[<?php echo $index; ?>][price]" class="form-control price" placeholder="Precio" required step="0.01" value="<?php echo $item['price']; ?>">
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
            <input type="number" name="total_amount" id="total_amount" class="form-control" readonly value="<?php echo $budget['total_amount']; ?>">
        </div>

        <div class="mb-3">
            <label for="validity_period" class="form-label">Período de Validez (días)</label>
            <input type="number" name="validity_period" id="validity_period" class="form-control" value="<?php echo $budget['validity_period']; ?>" required min="1">
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo htmlspecialchars($budget['notes']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Presupuesto</button>
        <a href="<?php echo url('budget.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('productList');
    const addProductBtn = document.getElementById('addProduct');
    let productCount = <?php echo count($budget['items']); ?>;

    function updateSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const subtotal = quantity * price;
        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(function(el) {
            total += parseFloat(el.textContent) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    function initializeRow(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity');
        const priceInput = row.querySelector('.price');

        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            priceInput.value = selectedOption.dataset.price;
            updateSubtotal(row);
        });

        quantityInput.addEventListener('input', function() {
            updateSubtotal(row);
        });

        priceInput.addEventListener('input', function() {
            updateSubtotal(row);
        });

        row.querySelector('.remove-product').addEventListener('click', function() {
            if (productList.children.length > 1) {
                row.remove();
                updateTotal();
            } else {
                alert('Debe haber al menos un producto en el presupuesto.');
            }
        });
    }

    addProductBtn.addEventListener('click', function() {
        const newRow = productList.children[0].cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(function(input) {
            input.value = '';
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${productCount}]`);
            }
        });
        newRow.querySelector('.subtotal').textContent = '0.00';
        productList.appendChild(newRow);
        initializeRow(newRow);
        productCount++;
    });

    productList.querySelectorAll('.product-item').forEach(initializeRow);

    // Inicializar el total
    updateTotal();

    // Validación del formulario
    document.getElementById('budgetForm').addEventListener('submit', function(event) {
        let isValid = true;
        const customerSelect = document.getElementById('customer_id');
        const productItems = document.querySelectorAll('.product-item');

        if (!customerSelect.value) {
            isValid = false;
            customerSelect.classList.add('is-invalid');
        } else {
            customerSelect.classList.remove('is-invalid');
        }

        productItems.forEach(function(item) {
            const productSelect = item.querySelector('.product-select');
            const quantity = item.querySelector('.quantity');
            const price = item.querySelector('.price');

            if (!productSelect.value || !quantity.value || !price.value) {
                isValid = false;
                [productSelect, quantity, price].forEach(el => el.classList.add('is-invalid'));
            } else {
                [productSelect, quantity, price].forEach(el => el.classList.remove('is-invalid'));
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Por favor, complete todos los campos requeridos correctamente.');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>