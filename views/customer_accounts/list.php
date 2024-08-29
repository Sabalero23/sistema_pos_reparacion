<div class="container mt-4">
    <h1 class="mb-4">Cuentas de Clientes</h1>
    
    <table class="table table-striped" id="customerAccountsTable">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Total Ventas</th>
                <th>Total Pagos</th>
                <th>Saldo Pendiente</th>
                <th>Última Venta</th>
                <th>Último Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?php echo htmlspecialchars($account['name']); ?></td>
                    <td><?php echo number_format($account['total_sales'], 2); ?> ARS</td>
                    <td><?php echo number_format($account['total_payments'], 2); ?> ARS</td>
                    <td><?php echo number_format($account['balance'], 2); ?> ARS</td>
                    <td><?php echo $account['last_sale_date'] ? date('d/m/Y', strtotime($account['last_sale_date'])) : 'N/A'; ?></td>
                    <td><?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></td>
                    <td>
                        <a href="<?php echo url('customer_accounts.php?action=view&id=' . $account['id']); ?>" class="btn btn-sm btn-info">Ver Detalle</a>
                        <?php if ($account['balance'] > 0): ?>
                            <button class="btn btn-sm btn-success realizar-pago" data-customer-id="<?php echo $account['id']; ?>" data-customer-name="<?php echo htmlspecialchars($account['name']); ?>" data-balance="<?php echo $account['balance']; ?>">Realizar Pago</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para registrar pago -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="customerId" name="customer_id">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Monto del Pago</label>
                        <input type="number" class="form-control" id="paymentAmount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Método de Pago</label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notas</label>
                        <textarea class="form-control" id="paymentNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="submitPayment">Registrar Pago</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#customerAccountsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "order": [[ 3, "desc" ]]
    });

    $('.realizar-pago').click(function() {
        var customerId = $(this).data('customer-id');
        var customerName = $(this).data('customer-name');
        var balance = $(this).data('balance');
        $('#customerId').val(customerId);
        $('#paymentAmount').attr('max', balance);
        $('#paymentModalLabel').text('Registrar Pago para ' + customerName);
        $('#paymentModal').modal('show');
    });

    $('#submitPayment').click(function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas registrar este pago?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, registrar pago',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("customer_accounts.php?action=add_payment"); ?>',
                    method: 'POST',
                    data: $('#paymentForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        if (response.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Hubo un problema al procesar la solicitud.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error AJAX:', textStatus, errorThrown);
                        console.error('Respuesta del servidor:', jqXHR.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud. Por favor, inténtalo de nuevo.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script>