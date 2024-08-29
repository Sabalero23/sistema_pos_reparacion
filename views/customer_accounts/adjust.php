<?php
// Asegúrate de que todas las variables necesarias estén definidas
if (!isset($customers)) {
    echo "Error: No se pueden cargar los datos de los clientes.";
    exit;
}
?>
<div class="container mt-4">
    <h1 class="mb-4">Ajustar Cuenta de Cliente</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="<?php echo url('customer_accounts.php?action=adjust'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="customer_id" class="form-label">Cliente</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>" <?php echo ($customer['id'] == ($customerId ?? '')) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($customer['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un cliente.
            </div>
        </div>
        
        <div class="mb-3">
            <label for="adjustment_type" class="form-label">Tipo de Ajuste</label>
            <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                <option value="credit">Crédito (Aumentar Saldo)</option>
                <option value="debit">Débito (Disminuir Saldo)</option>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un tipo de ajuste.
            </div>
        </div>
        
        <div class="mb-3">
            <label for="amount" class="form-label">Monto del Ajuste</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
            </div>
            <div class="invalid-feedback">
                Por favor ingrese un monto válido mayor a cero.
            </div>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            <div class="invalid-feedback">
                Por favor ingrese una descripción.
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Realizar Ajuste</button>
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

document.getElementById('adjustment_type').addEventListener('change', function() {
    var amountInput = document.getElementById('amount');
    if (this.value === 'debit') {
        amountInput.max = <?php echo $account['balance'] ?? 0; ?>;
    } else {
        amountInput.removeAttribute('max');
    }
});
</script>