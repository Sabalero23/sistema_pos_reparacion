<?php
// Asegúrate de que $reservation y $reservationItems estén disponibles desde el controlador
if (!isset($reservation) || !isset($reservationItems)) {
    die("Error: Datos de reserva no disponibles.");
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Convertir Reserva a Venta</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form id="convertForm" action="<?php echo url('reservations.php?action=convert&id=' . $reservation['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="card mb-4">
            <div class="card-header">
                Información de la Reserva
            </div>
            <div class="card-body">
                <p><strong>ID de Reserva:</strong> <?php echo htmlspecialchars($reservation['id']); ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($reservation['customer_name'] ?? 'N/A'); ?></p>
                <p><strong>Fecha de Reserva:</strong> <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                <p><strong>Estado Actual:</strong> <?php echo htmlspecialchars(ucfirst($reservation['status'])); ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Productos
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservationItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>
                                    <input type="number" class="form-control quantity" name="items[<?php echo $item['id']; ?>][quantity]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" required readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control price" name="items[<?php echo $item['id']; ?>][price]" value="<?php echo htmlspecialchars($item['price']); ?>" step="0.01" required readonly>
                                </td>
                                <td class="subtotal"><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th id="total"><?php echo number_format($reservation['total_amount'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Método de Pago</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="">Seleccione un método de pago</option>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
                <option value="transferencia">Transferencia</option>
                <option value="otros">Otros</option>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un método de pago.
            </div>
        </div>

        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($reservation['total_amount']); ?>">

        <button type="submit" class="btn btn-primary">Convertir a Venta</button>
        <a href="<?php echo url('reservations.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('convertForm');

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Si necesitas cualquier cálculo dinámico, puedes agregarlo aquí
    // Por ejemplo, si quieres permitir modificar las cantidades:
    /*
    document.querySelectorAll('.quantity, .price').forEach(input => {
        input.addEventListener('input', updateSubtotalAndTotal);
    });

    function updateSubtotalAndTotal() {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(function(row) {
            const quantity = parseFloat(row.querySelector('.quantity').value);
            const price = parseFloat(row.querySelector('.price').value);
            const subtotal = quantity * price;
            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total').textContent = total.toFixed(2);
        document.querySelector('input[name="total_amount"]').value = total.toFixed(2);
    }
    */
});
</script>