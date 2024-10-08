<?php
// Asegúrate de que $order esté disponible y contenga los datos de la orden de servicio
?>
<div class="container mt-4">
    <h1 class="mb-4">Editar Orden de Servicio #<?php echo htmlspecialchars($order['order_number']); ?></h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo url('services.php?action=edit&id=' . $order['id']); ?>" method="POST">
        <div class="mb-3">
            <label for="customer_id" class="form-label">Cliente</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                <option value="<?php echo $order['customer_id']; ?>"><?php echo htmlspecialchars($order['customer_name']); ?></option>
            </select>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Marca</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($order['brand']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="model" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($order['model']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="serial_number" class="form-label">Número de Serie</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number" value="<?php echo htmlspecialchars($order['serial_number']); ?>">
        </div>

        <div class="mb-3">
            <label for="warranty" class="form-label">Garantía</label>
            <select name="warranty" id="warranty" class="form-control" required>
                <option value="0" <?php echo $order['warranty'] == 0 ? 'selected' : ''; ?>>No</option>
                <option value="1" <?php echo $order['warranty'] == 1 ? 'selected' : ''; ?>>Sí</option>
            </select>
        </div>

        <div id="services-container">
            <h3>Servicios</h3>
            <?php foreach ($order['items'] as $index => $item): ?>
                <div class="service-item mb-3">
                    <input type="text" class="form-control mb-2" name="services[<?php echo $index; ?>][description]" placeholder="Descripción del servicio" value="<?php echo htmlspecialchars($item['description']); ?>" required>
                    <input type="number" class="form-control mb-2 service-cost" name="services[<?php echo $index; ?>][cost]" placeholder="Costo" value="<?php echo $item['cost']; ?>" step="0.01" required>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-service" class="btn btn-secondary mb-3">Agregar Servicio</button>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Monto Total</label>
            <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?php echo $order['total_amount']; ?>" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="prepaid_amount" class="form-label">Monto Prepagado</label>
            <input type="number" class="form-control" id="prepaid_amount" name="prepaid_amount" value="<?php echo $order['prepaid_amount']; ?>" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Orden de Servicio</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addServiceBtn = document.getElementById('add-service');
    const servicesContainer = document.getElementById('services-container');
    let serviceIndex = <?php echo count($order['items']); ?>;

    addServiceBtn.addEventListener('click', function() {
        const serviceItem = document.createElement('div');
        serviceItem.classList.add('service-item', 'mb-3');
        serviceItem.innerHTML = `
            <input type="text" class="form-control mb-2" name="services[${serviceIndex}][description]" placeholder="Descripción del servicio" required>
            <input type="number" class="form-control mb-2 service-cost" name="services[${serviceIndex}][cost]" placeholder="Costo" step="0.01" required>
        `;
        servicesContainer.appendChild(serviceItem);
        serviceIndex++;
        updateTotalAmount();
    });

    servicesContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('service-cost')) {
            updateTotalAmount();
        }
    });

    function updateTotalAmount() {
        const serviceCosts = document.querySelectorAll('.service-cost');
        let total = 0;
        serviceCosts.forEach(function(cost) {
            total += parseFloat(cost.value) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    updateTotalAmount();
});
</script>