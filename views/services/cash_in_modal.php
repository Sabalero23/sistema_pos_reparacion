<style>
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal-content {
        z-index: 1100 !important;
    }
</style>

<div class="modal fade" id="cashInModal" tabindex="-1" aria-labelledby="cashInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashInModalLabel">Registrar Ingreso de Efectivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo url('services.php?action=handle_cash_in'); ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Monto</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($pendingCashIn['amount']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <input type="text" class="form-control" id="notes" name="notes" value="Seña de <?php echo htmlspecialchars($pendingCashIn['customer_name']); ?> de la orden <?php echo htmlspecialchars($pendingCashIn['order_number']); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cashInModal = new bootstrap.Modal(document.getElementById('cashInModal'), {
        backdrop: 'static',
        keyboard: false
    });
    
    // Asegúrate de que cualquier modal previo esté cerrado
    var modals = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
    if (modals) {
        modals.hide();
    }

    // Abre el modal de ingreso de efectivo
    cashInModal.show();

    // Asegúrate de que el modal esté en la parte superior
    document.getElementById('cashInModal').style.zIndex = "1050";
});
</script>