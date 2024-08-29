<div class="container mt-4">
    <h1 class="mb-4">Recibir Compra #<?php echo $purchase['id']; ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
            <?php if (strpos($error, 'Error al actualizar el stock del producto') !== false): ?>
                <br>
                <strong>Sugerencia:</strong> Verifique que el producto exista, tenga suficiente stock disponible y que los datos sean correctos.
                <br>
                <strong>Acción recomendada:</strong> Revise los logs del servidor para obtener más detalles sobre el error.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form id="receivePurchaseForm" action="<?php echo url('purchases.php?action=receive&id=' . $purchase['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Ordenada</th>
                        <th>Cantidad Recibida</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchaseItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?> (ID: <?php echo $item['product_id']; ?>)</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>
                                <input type="number" class="form-control quantity-input" 
                                       name="items[<?php echo $item['id']; ?>][received_quantity]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="0" max="<?php echo $item['quantity']; ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese una cantidad válida.</div>
                            </td>
                            <td>
                                <input type="number" class="form-control price-input" 
                                       name="items[<?php echo $item['id']; ?>][price]" 
                                       value="<?php echo number_format($item['price'], 2, '.', ''); ?>" 
                                       min="0" step="0.01" required>
                                <div class="invalid-feedback">Por favor, ingrese un precio válido.</div>
                            </td>
                            <td>
                                <span class="subtotal">
                                    <?php echo number_format($item['quantity'] * $item['price'], 2, '.', ','); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td><strong id="total"><?php echo number_format($purchase['total_amount'], 2, '.', ','); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <button type="submit" class="btn btn-primary">Confirmar Recepción</button>
        <a href="<?php echo url('purchases.php?action=view&id=' . $purchase['id']); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('receivePurchaseForm');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const priceInputs = document.querySelectorAll('.price-input');
    
    function updateSubtotalAndTotal() {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value);
            const price = parseFloat(row.querySelector('.price-input').value);
            const subtotal = quantity * price;
            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total').textContent = total.toFixed(2);
    }

    quantityInputs.forEach(input => {
        input.addEventListener('change', updateSubtotalAndTotal);
    });

    priceInputs.forEach(input => {
        input.addEventListener('change', updateSubtotalAndTotal);
    });

    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Inicial cálculo de subtotales y total
    updateSubtotalAndTotal();
});
</script>