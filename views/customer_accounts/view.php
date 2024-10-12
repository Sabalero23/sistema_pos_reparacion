<?php
// Asegúrate de que $account, $installments y $payments estén definidos y contengan todos los datos necesarios
if (!isset($account) || !is_array($account) || !isset($installments) || !isset($payments)) {
    echo "Error: No se pueden cargar los datos de la cuenta.";
    exit;
}

// Cálculos para el resumen de la cuenta
$totalAmount = $account['total_amount'] ?? 0;
$downPayment = $account['down_payment'] ?? 0;
$numInstallments = $account['num_installments'] ?? 1;
$balance = $account['balance'] ?? $totalAmount - $downPayment;
$installmentAmount = $numInstallments > 0 ? ($totalAmount - $downPayment) / $numInstallments : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta de Cliente: <?php echo htmlspecialchars($account['customer_name'] ?? 'Desconocido'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            h1 {
                font-size: 1.5rem;
            }
            .btn {
                padding: .375rem .75rem;
                font-size: .875rem;
            }
            .table {
                font-size: .875rem;
            }
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Cuenta de Cliente: <?php echo htmlspecialchars($account['customer_name'] ?? 'Desconocido'); ?></h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen de la Cuenta</h5>
            <p class="card-text"><strong>Monto Total:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
            <p class="card-text"><strong>Entrega Inicial:</strong> $<?php echo number_format($downPayment, 2); ?></p>
            <p class="card-text"><strong>Saldo Pendiente:</strong> $<?php echo number_format($balance, 2); ?></p>
            <p class="card-text"><strong>Número de Cuotas:</strong> <?php echo $numInstallments; ?></p>
            <p class="card-text"><strong>Monto de Cuota:</strong> $<?php echo number_format($installmentAmount, 2); ?></p>
            <p class="card-text"><strong>Estado de la Cuenta:</strong> <?php echo ucfirst($account['status'] ?? 'Desconocido'); ?></p>
            <p class="card-text"><strong>Fecha de Primera Cuota:</strong> <?php echo isset($account['first_due_date']) ? date('d/m/Y', strtotime($account['first_due_date'])) : 'N/A'; ?></p>
            <p class="card-text"><strong>Fecha de Próxima Cuota:</strong> <?php echo isset($account['next_due_date']) ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></p>
            <p class="card-text"><strong>Fecha de Último Pago:</strong> <?php echo isset($account['last_payment_date']) ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></p>
        </div>
    </div>

    <h2 class="mb-3">Cuotas</h2>
    <?php if (empty($installments)): ?>
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
                <?php foreach ($installments as $installment): ?>
                    <tr>
                        <td><?php echo $installment['installment_number'] ?? 'N/A'; ?></td>
                        <td><?php echo isset($installment['due_date']) ? date('d/m/Y', strtotime($installment['due_date'])) : 'N/A'; ?></td>
                        <td>$<?php echo number_format($installment['amount'] ?? 0, 2); ?></td>
                        <td><?php echo ucfirst($installment['status'] ?? 'Desconocido'); ?></td>
                        <td><?php echo isset($installment['paid_date']) ? date('d/m/Y', strtotime($installment['paid_date'])) : 'N/A'; ?></td>
                        <td>$<?php echo number_format($installment['paid_amount'] ?? 0, 2); ?></td>
                        <td>$<?php echo number_format($installment['late_fee'] ?? 0, 2); ?></td>
                        <td>
                            <?php if (($installment['status'] ?? '') !== 'pagada'): ?>
                                <button class="btn btn-primary btn-sm pay-installment" 
                                        data-installment-id="<?php echo $installment['id'] ?? ''; ?>" 
                                        data-amount="<?php echo ($installment['amount'] ?? 0) - ($installment['paid_amount'] ?? 0); ?>">
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

    <!-- Modal para procesar el pago -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Procesar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="installmentId" name="installmentId">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Monto a Pagar</label>
                        <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentDate" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="paymentDate" name="paymentDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Método de Pago</label>
                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notas</label>
                        <textarea class="form-control" id="paymentNotes" name="paymentNotes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="processPayment">Procesar Pago</button>
            </div>
        </div>
    </div>
</div>


    <h2 class="mb-3 mt-5">Historial de Pagos</h2>
    <?php if (empty($payments)): ?>
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
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo isset($payment['payment_date']) ? date('d/m/Y', strtotime($payment['payment_date'])) : 'N/A'; ?></td>
                            <td>$<?php echo number_format($payment['amount'] ?? 0, 2); ?></td>
                            <td><?php echo ucfirst($payment['payment_method'] ?? 'Desconocido'); ?></td>
                            <td><?php echo htmlspecialchars($payment['notes'] ?? ''); ?></td>
                            <td>
                                <a href="<?php echo url('customer_accounts.php?action=print_receipt&payment_id=' . ($payment['id'] ?? '')); ?>" class="btn btn-sm btn-info" target="_blank">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
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

    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    $('.pay-installment').on('click', function() {
        const installmentId = $(this).data('installment-id');
        const amount = $(this).data('amount');
        $('#installmentId').val(installmentId);
        $('#paymentAmount').val(amount);
        $('#paymentDate').val(new Date().toISOString().split('T')[0]); // Set current date as default
        paymentModal.show();
    });

    $('#processPayment').on('click', function() {
        const formData = new FormData($('#paymentForm')[0]);
        $.ajax({
            url: 'customer_accounts.php?action=process_payment',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Pago procesado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error al procesar el pago: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                alert('Error al procesar el pago. Por favor, revise la consola para más detalles.');
            }
        });
    });
});
</script>
</body>
</html>