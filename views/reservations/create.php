<div class="container mt-4">
    <h1 class="mb-4">Nueva Reserva</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="reservationForm" action="<?php echo url('reservations.php?action=create'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="customer_id" class="form-label">Cliente</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Por favor, seleccione un cliente.</div>
        </div>

        <div id="productList">
            <div class="product-item mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-select product-select" name="items[0][product_id]" required>
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, seleccione un producto.</div>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control quantity" name="items[0][quantity]" placeholder="Cantidad" required min="1">
                        <div class="invalid-feedback">Por favor, ingrese una cantidad válida.</div>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control price" name="items[0][price]" placeholder="Precio" readonly>
                    </div>
                    <div class="col-md-1">
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

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Crear Reserva</button>
        <a href="<?php echo url('reservations.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservationForm');
    const productList = document.getElementById('productList');
    const addProductBtn = document.getElementById('addProduct');
    let productCount = 1;

    addProductBtn.addEventListener('click', function() {
        const newProduct = document.querySelector('.product-item').cloneNode(true);
        newProduct.querySelector('.product-select').name = `items[${productCount}][product_id]`;
        newProduct.querySelector('.quantity').name = `items[${productCount}][quantity]`;
        newProduct.querySelector('.price').name = `items[${productCount}][price]`;
        newProduct.querySelector('.quantity').value = '';
        newProduct.querySelector('.price').value = '';
        productList.appendChild(newProduct);
        productCount++;
    });

    productList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            if (productList.children.length > 1) {
                e.target.closest('.product-item').remove();
                updateTotal();
            } else {
                alert('Debe haber al menos un producto en la reserva.');
            }
        }
    });

    productList.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity')) {
            const item = e.target.closest('.product-item');
            const select = item.querySelector('.product-select');
            const quantity = item.querySelector('.quantity');
            const price = item.querySelector('.price');

            if (select.value && quantity.value) {
                const selectedOption = select.options[select.selectedIndex];
                const productPrice = parseFloat(selectedOption.dataset.price);
                price.value = (productPrice * parseInt(quantity.value)).toFixed(2);
            } else {
                price.value = '';
            }

            updateTotal();
        }
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.price').forEach(function(priceInput) {
            total += parseFloat(priceInput.value) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>