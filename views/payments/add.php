<div class="container mt-4">
    <h1 class="mb-4">Realizar Pago para <?php echo htmlspecialchars($account['name']); ?></h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Resumen de la Cuenta</h5>
            <p class="card-text"><strong>Saldo Pendiente:</strong> $<?php echo number_format($account['balance'], 2); ?></p>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo url('payments.php?action=add&customer_id=' . $account['id']); ?>" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="customer_id" value="<?php echo $account['id']; ?>">
        
        <div class="mb-3">
            <label for="amount" class="form-label">Monto del Pago</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" max="<?php echo $account['balance']; ?>" required>
            </div>
            <div class="invalid-feedback">
                Por favor ingrese un monto válido (máximo $<?php echo number_format($account['balance'], 2); ?>).
            </div>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Método de Pago</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="">Seleccione un método de pago</option>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
                <option value="transferencia">Transferencia Bancaria</option>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un método de pago.
            </div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Pago</button>
        <a href="<?php echo url('customer_accounts.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
(function () {
  'use strict'

  var forms = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>