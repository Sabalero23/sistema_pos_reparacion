<?php
// Asegúrate de que $account esté definido y contenga todos los datos necesarios
if (!isset($account) || !is_array($account)) {
    echo "Error: No se pueden cargar los datos de la cuenta.";
    exit;
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Cuenta de Cliente: <?php echo htmlspecialchars($account['customer_name'] ?? 'Desconocido'); ?></h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen de la Cuenta</h5>
            <p class="card-text"><strong>Monto Total:</strong> $<?php echo number_format($account['total_amount'], 2); ?></p>
            <p class="card-text"><strong>Entrega Inicial:</strong> $<?php echo number_format($account['down_payment'], 2); ?></p>
            <p class="card-text"><strong>Saldo Pendiente:</strong> $<?php echo number_format($account['balance'], 2); ?></p>
            <p class="card-text"><strong>Número de Cuotas:</strong> <?php echo $account['num_installments']; ?></p>
            <p class="card-text"><strong>Monto de Cuota:</strong> $<?php echo number_format($account['installment_amount'], 2); ?></p>
            <p class="card-text"><strong>Estado de la Cuenta:</strong> <?php echo ucfirst($account['status']); ?></p>
            <p class="card-text"><strong>Fecha de Primera Cuota:</strong> <?php echo date('d/m/Y', strtotime($account['first_due_date'])); ?></p>
            <p class="card-text"><strong>Fecha de Próxima Cuota:</strong> <?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></p>
            <p class="card-text"><strong>Fecha de Último Pago:</strong> <?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></p>
        </div>
    </div>

    <h2 class="mb-3">Cuotas</h2>
    <?php if (empty($account['installments'])): ?>
        <div class="alert alert-info">No hay cuotas registradas para esta cuenta.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="installmentsTable">
                <thead>
                    <tr>
                        <th>Número de Cuota</th>
                        <th>Fecha de Vencimiento</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha de Pago</th>
                        <th>Monto Pagado</th>
                        <th>Mora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($account['installments'] as $installment): ?>
        <tr>
            <td><?php echo $installment['installment_number']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($installment['due_date'])); ?></td>
            <td>$<?php echo number_format($installment['amount'], 2); ?></td>
            <td><?php echo ucfirst($installment['status']); ?></td>
            <td><?php echo isset($installment['paid_date']) ? date('d/m/Y', strtotime($installment['paid_date'])) : 'N/A'; ?></td>
            <td>$<?php echo number_format(isset($installment['paid_amount']) ? $installment['paid_amount'] : 0, 2); ?></td>
            <td>$<?php echo number_format(isset($installment['late_fee']) ? $installment['late_fee'] : 0, 2); ?></td>
            <td>
                <?php if ($installment['status'] != 'pagada'): ?>
                    <button class="btn btn-sm btn-primary pay-installment" 
                            data-installment-id="<?php echo $installment['id']; ?>"
                            data-amount="<?php echo $installment['amount'] - (isset($installment['paid_amount']) ? $installment['paid_amount'] : 0); ?>">
                        Pagar
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
    <?php if (empty($account['payments'])): ?>
        <div class="alert alert-info">No hay pagos registrados para esta cuenta.</div>
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
                    <?php foreach ($account['payments'] as $payment): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                            <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo ucfirst($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($payment['notes'] ?? ''); ?></td>
                            <td>
                                <a href="<?php echo url('customer_accounts.php?action=print_receipt&id=' . $payment['id']); ?>" class="btn btn-sm btn-info" target="_blank">
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

<!-- Modal para pago de cuota -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pagar Cuota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="installmentId" name="installment_id">
                    <input type="hidden" name="account_id" value="<?php echo $account['id']; ?>">
                    <input type="hidden" name="customer_id" value="<?php echo $account['customer_id']; ?>">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Monto a Pagar</label>
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
                        <label for="paymentDate" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notas</label>
                        <textarea class="form-control" id="paymentNotes" name="notes"></textarea>
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
    $('#installmentsTable').DataTable({
        "order": [[ 0, "asc" ]],
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

    $('.pay-installment').click(function() {
        var installmentId = $(this).data('installment-id');
        var amount = $(this).data('amount');
        $('#installmentId').val(installmentId);
        $('#paymentAmount').val(amount);
        $('#paymentAmount').attr('max', amount);
        $('#paymentDate').val(new Date().toISOString().split('T')[0]);
        $('#paymentModal').modal('show');
    });

    $('#submitPayment').click(function() {
        if (!$('#paymentForm')[0].checkValidity()) {
            $('#paymentForm')[0].reportValidity();
            return;
        }

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