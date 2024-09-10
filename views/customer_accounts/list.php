<?php
// Asegúrate de que $accounts esté definido y sea un array
if (!isset($accounts) || !is_array($accounts)) {
    echo "Error: No se pueden cargar las cuentas de clientes.";
    exit;
}

// Obtener clientes con cuotas vencidas o próximas
$clientsWithIssues = getClientsWithOverdueOrUpcomingInstallments();

// Función para generar un token de acceso único
function generateAccessToken() {
    return bin2hex(random_bytes(32));
}
?>

<div class="container mt-4">
    <?php if (empty($accounts)): ?>
        <div class="alert alert-info">No hay cuentas de clientes registradas.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="customerAccountsTable">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Monto Total</th>
                        <th>Saldo Pendiente</th>
                        <th>Cuotas Totales</th>
                        <th>Cuotas Pendientes</th>
                        <th>Próximo Vencimiento</th>
                        <th>Último Pago</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($account['customer_name']); ?></td>
                            <td><?php echo number_format($account['total_amount'], 2); ?> ARS</td>
                            <td><?php echo number_format($account['balance'], 2); ?> ARS</td>
                            <td><?php echo $account['num_installments']; ?></td>
                            <td><?php echo $account['pending_installments']; ?></td>
                            <td><?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></td>
                            <td><?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></td>
                            <td><?php echo ucfirst($account['status']); ?></td>
                            <td>
                                <a href="<?php echo url('customer_accounts.php?action=view&id=' . $account['id']); ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <?php if (hasPermission('customer_accounts_edit')): ?>
                                    <a href="<?php echo url('customer_accounts.php?action=edit&id=' . $account['id']); ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                <?php endif; ?>
                                <?php if ($account['balance'] > 0): ?>

                                <?php endif; ?>
                                <?php
                                $accessToken = generateAccessToken();
                                saveAccessToken($account['id'], $accessToken);
                                $publicViewUrl = url("customer_accounts_view.php?token={$accessToken}");
                                $whatsappMessage = urlencode("Hola, aquí puedes ver los detalles de tu cuenta: {$publicViewUrl}");
                                
                                if (isset($account['customer_phone']) && !empty($account['customer_phone'])) {
                                    $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $account['customer_phone']) . "?text={$whatsappMessage}";
                                    ?>
                                    <a href="<?php echo $whatsappUrl; ?>" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                                    </a>
                                <?php
                                } else {
                                    ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="No hay número de teléfono disponible">
                                        <i class="fab fa-whatsapp"></i> WhatsApp no disponible
                                    </button>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (hasPermission('customer_accounts_adjust')): ?>
        <div class="mt-4">
            <button onclick="applyLateFees()" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle"></i> Aplicar Cargos por Mora
            </button>
            <small class="text-muted ms-2">
                Esto aplicará cargos por mora a todas las cuentas con cuotas vencidas.
            </small>
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
                    <input type="hidden" id="accountId" name="account_id">
                    <input type="hidden" id="customerId" name="customer_id">
                    <div class="mb-3">
                        <label for="installmentSelect" class="form-label">Seleccionar Cuota</label>
                        <select class="form-control" id="installmentSelect" name="installment_id" required>
                            <!-- Las opciones se llenarán dinámicamente con JavaScript -->
                        </select>
                    </div>
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

<!-- Modal para clientes con problemas de pago -->
<div class="modal fade" id="clientIssuesModal" tabindex="-1" aria-labelledby="clientIssuesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientIssuesModalLabel">Clientes con Cuotas Vencidas o Próximas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // ... (código anterior sin cambios)

    // Función para mostrar el modal de clientes con problemas
    function showClientIssuesModal() {
        var modalBody = $('#clientIssuesModal .modal-body');
        modalBody.empty();

        if (clientsWithIssues.length > 0) {
            var table = $('<table class="table table-striped">').append(
                '<thead><tr><th>Cliente</th><th>Cuotas Vencidas</th><th>Próximo Vencimiento</th></tr></thead>'
            );
            var tbody = $('<tbody>');

            $.each(clientsWithIssues, function(index, client) {
                var row = $('<tr>').append(
                    $('<td>').text(client.name),
                    $('<td>').text(client.overdue_installments),
                    $('<td>').text(client.next_due_date ? new Date(client.next_due_date).toLocaleDateString() : 'N/A')
                );
                tbody.append(row);
            });

            table.append(tbody);
            modalBody.append(table);
        } else {
            modalBody.append('<p>No hay clientes con cuotas vencidas o próximas a vencer.</p>');
        }

        $('#clientIssuesModal').modal('show');
    }

    // Mostrar el modal automáticamente si hay clientes con problemas
    if (clientsWithIssues.length > 0) {
        showClientIssuesModal();
    }

    // ... (resto del código sin cambios)
});

// Variable global para almacenar los clientes con problemas
var clientsWithIssues = <?php echo json_encode($clientsWithIssues); ?>;
</script>