<?php
// Asegúrate de que esta función esté disponible o defínela en service_functions.php
function getCustomerInfo($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name, phone FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Órdenes de Servicio</h1>
    <a href="<?php echo url('services.php?action=create'); ?>" class="btn btn-primary mb-3">Nueva Orden de Servicio</a>
    
    <table id="serviceOrdersTable" class="table table-striped">
        <thead>
            <tr>
                <th>Número de Orden</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): 
                $customerInfo = getCustomerInfo($order['customer_id']);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <td><?php echo number_format($order['total_amount'], 2); ?></td>
                <td>
                    <a href="<?php echo url('services.php?action=view&id=' . $order['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                    <?php if (!empty($customerInfo['phone'])): ?>
                    <a href="#" class="btn btn-sm btn-success send-whatsapp" 
                       data-phone="<?php echo htmlspecialchars($customerInfo['phone']); ?>"
                       data-order="<?php echo htmlspecialchars($order['order_number']); ?>"
                       data-customer="<?php echo htmlspecialchars($customerInfo['name']); ?>">
                        Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const whatsappButtons = document.querySelectorAll('.send-whatsapp');
    whatsappButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const phone = this.getAttribute('data-phone');
            const orderNumber = this.getAttribute('data-order');
            const customerName = this.getAttribute('data-customer');
            const message = encodeURIComponent(`Hola ${customerName}, su orden de trabajo ha sido creada con éxito. Puede hacer el seguimiento en el siguiente link: https://taller.whaticket.com.ar/public/seguimiento.php?order_number=${orderNumber}`);
            const whatsappUrl = `https://api.whatsapp.com/send?phone=${phone}&text=${message}`;
            window.open(whatsappUrl, '_blank');
        });
    });
});
$(document).ready(function() {
    $('#serviceOrdersTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[2, "desc"]]
    });
});
</script>