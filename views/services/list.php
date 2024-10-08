<?php
// Asegúrate de que esta función esté disponible o defínela en service_functions.php
function getCustomerInfo($customerId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name, phone FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function generateWhatsAppUrl($phoneNumber, $message, $orderNumber) {
    // Obtener la URL base del sistema
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    
    // Crear la URL de seguimiento
    $trackingUrl = $baseUrl . "/public/seguimiento.php?order_number=" . urlencode($orderNumber);
    
    // Crear el mensaje con la URL de seguimiento
    $fullMessage = $message . "\n\nSigue tu orden aquí: " . $trackingUrl;
    
    // Generar la URL de WhatsApp
    $whatsappUrl = "https://api.whatsapp.com/send?phone=" . urlencode($phoneNumber) . "&text=" . urlencode($fullMessage);
    
    return $whatsappUrl;
}

// Asegúrate de que $orders esté definido antes de este punto, por ejemplo:
// $orders = getAllOrders(); // Función que deberías tener definida para obtener todas las órdenes
?>

<div class="container mt-4">
    <h1 class="mb-4">Órdenes de Servicio</h1>
    <a href="<?php echo url('services.php?action=create'); ?>" class="btn btn-primary mb-3">Nueva Orden de Servicio</a>
    
    <table id="serviceOrdersTable" class="table table-striped">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Marca</th>
                <th>Modelo</th>
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
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td data-order="<?php echo strtotime($order['created_at']); ?>">
                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </td>
                <td><?php echo htmlspecialchars($order['brand'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($order['model'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <td data-order="<?php echo $order['total_amount']; ?>">
                    <?php echo number_format($order['total_amount'], 2, ',', '.'); ?>
                </td>
                <td>
    <a href="<?php echo url('services.php?action=view&id=' . $order['id']); ?>" class="btn btn-sm btn-info">Ver</a>
    <?php if (hasPermission('services_edit')): ?>
        <a href="<?php echo url('services.php?action=edit&id=' . $order['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
    <?php endif; ?>
    <?php if (!empty($customerInfo['phone'])): 
        $message = "Hola {$customerInfo['name']}, su orden de trabajo ha sido creada con éxito.";
        $whatsappUrl = generateWhatsAppUrl($customerInfo['phone'], $message, $order['order_number']);
    ?>
    <a href="<?php echo $whatsappUrl; ?>" class="btn btn-sm btn-success" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#serviceOrdersTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[1, "desc"]], // Ordenar por la columna de fecha de forma descendente
        "columnDefs": [
            { "orderable": false, "targets": 6 } // Hace que la columna de acciones no sea ordenable
        ]
    });
});
</script>