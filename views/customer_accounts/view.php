<div class="container mt-4">
    <h1 class="mb-4">Cuenta de Cliente: <?php echo htmlspecialchars($account['name'] ?? 'Desconocido'); ?></h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen de la Cuenta</h5>
            <p class="card-text"><strong>Total Ventas:</strong> $<?php echo number_format($account['total_sales'] ?? 0, 2); ?></p>
            <p class="card-text"><strong>Total Pagos:</strong> $<?php echo number_format($account['total_payments'] ?? 0, 2); ?></p>
            <p class="card-text"><strong>Saldo Pendiente:</strong> <span class="<?php echo ($account['balance'] ?? 0) > 0 ? 'text-danger' : 'text-success'; ?>">$<?php echo number_format($account['balance'] ?? 0, 2); ?></span></p>
        </div>
    </div>

    <h2 class="mb-3">Ventas y Pagos</h2>
    <?php if (empty($sales)): ?>
        <div class="alert alert-info">No hay ventas registradas para este cliente.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="salesTable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto Total</th>
                        <th>Monto Pagado</th>
                        <th>Balance</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($sale['sale_date'])); ?></td>
                            <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td>$<?php echo number_format($sale['amount_paid'], 2); ?></td>
                            <td>$<?php echo number_format($sale['balance'], 2); ?></td>
                            <td><?php echo htmlspecialchars($sale['status']); ?></td>
                            <td>
                                <a href="<?php echo url('sales.php?action=view&id=' . $sale['id']); ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <?php if ($sale['balance'] > 0): ?>
                                    <button class="btn btn-sm btn-success register-payment" data-sale-id="<?php echo $sale['id']; ?>" data-balance="<?php echo $sale['balance']; ?>">
                                        <i class="fas fa-money-bill-wave"></i> Registrar Pago
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 class="mb-3 mt-5">Historial de Pagos</h2>
    <?php if (empty($payments)): ?>
        <div class="alert alert-info">No hay pagos registrados para este cliente.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método de Pago</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                            <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($payment['notes'] ?? ''); ?></td>
                            <td>
                                <a href="<?php echo url('/../views/customer_accounts/payment_receipt.php?id=' . $payment['id']); ?>" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-print"></i> Imprimir Recibo
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
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
                    <input type="hidden" id="saleId" name="sale_id">
                    <input type="hidden" name="customer_id" value="<?php echo $account['id']; ?>">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Monto del Pago</label>
                        <input type="number" class="form-control" id="paymentAmount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Método de Pago</label>
                        <select class="form-control" id="paymentMethod" name="payment_method" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
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
    $('#salesTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    $('#paymentsTable').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    $('.register-payment').click(function() {
        var saleId = $(this).data('sale-id');
        var balance = $(this).data('balance');
        $('#saleId').val(saleId);
        $('#paymentAmount').attr('max', balance);
        $('#paymentModal').modal('show');
    });

    $('#submitPayment').click(function() {
        $.ajax({
            url: '<?php echo url("customer_accounts.php?action=add_payment"); ?>',
            method: 'POST',
            data: $('#paymentForm').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    });
});
</script>