<?php
// Obtén el ID de la orden desde la URL
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    die("ID de orden no proporcionado");
}

// Obtén los detalles de la orden
$order = getServiceOrder($order_id);
$order_status_history = getOrderStatusHistory($order_id);
$order_notes = getOrderNotes($order_id);
$order_parts = getOrderParts($order_id);
// obtenemos la información de la empresa (agregar al inicio con las otras consultas)
$companyInfo = getCompanyInfo();

// Calcula el costo total de las piezas
$total_parts_cost = array_sum(array_map(function($part) {
    return $part['cost'] * $part['quantity'];
}, $order_parts));

// Recalcula el saldo
$new_balance = $order['total_amount'] + $total_parts_cost - $order['prepaid_amount'];

// Función para formatear el número de teléfono para WhatsApp
function formatPhoneForWhatsApp($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 2) !== '54') {
        $phone = '54' . $phone;
    }
    return $phone;
}

$isClosed = ($order['status'] ?? '') === 'cerrado';
$googleMapsUrl = !empty($companyInfo['google_maps_url']) ? $companyInfo['google_maps_url'] : '#';
$whatsappMessage = urlencode("Hola " . ($order['customer_name'] ?? '') . ", su Orden de trabajo finalizó con éxito. Puede pasar a retirar su Equipo. Por favor calificá nuestra atención dejando una Reseña aquí " . $googleMapsUrl);
$whatsappLink = "https://wa.me/" . formatPhoneForWhatsApp($order['phone'] ?? '') . "?text=" . $whatsappMessage;

// Calcula el costo total de las piezas
$total_parts_cost = array_sum(array_column($order_parts ?? [], 'cost'));

// Recalcula el saldo
$new_balance = ($order['total_amount'] ?? 0) + $total_parts_cost - ($order['prepaid_amount'] ?? 0);
?>

<div class="container mt-4">
    <h1 class="mb-4">Orden de Servicio #<?php echo htmlspecialchars($order['order_number'] ?? ''); ?></h1>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Detalles del Cliente</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? 'No especificado'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'No especificado'); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'No especificado'); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($order['address'] ?? 'No especificada'); ?></p>
        </div>
        <div class="col-md-6">
            <h3>Detalles del Dispositivo</h3>
            <p><strong>Marca:</strong> <?php echo htmlspecialchars($order['brand'] ?? 'No especificada'); ?></p>
            <p><strong>Modelo:</strong> <?php echo htmlspecialchars($order['model'] ?? 'No especificado'); ?></p>
            <p><strong>Número de Serie:</strong> <?php echo htmlspecialchars($order['serial_number'] ?? 'No especificado'); ?></p>
        </div>
    </div>

    <h3 class="mt-4">Servicios</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Costo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] ?? [] as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                <td><?php echo number_format($item['cost'] ?? 0, 2); ?> ARS</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Resumen Financiero</h3>
            <table class="table">
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><?php echo number_format($order['total_amount'] ?? 0, 2); ?> ARS</td>
                </tr>
                <tr>
                    <td><strong>Monto Prepago:</strong></td>
                    <td><?php echo number_format($order['prepaid_amount'] ?? 0, 2); ?> ARS</td>
                </tr>
                <tr>
                    <td><strong>Piezas Utilizadas:</strong></td>
                    <td><?php echo number_format($total_parts_cost, 2); ?> ARS</td>
                </tr>
                <tr>
                    <td><strong>Saldo:</strong></td>
                    <td><?php echo number_format($new_balance, 2); ?> ARS</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h3>Estado de la Orden</h3>
            <p><strong>Estado Actual:</strong> <?php echo htmlspecialchars($order['status'] ?? 'No especificado'); ?></p>
            <form action="<?php echo url('services.php?action=update_status&id=' . ($order['id'] ?? '')); ?>" method="post">
                <div class="mb-3">
                    <label for="status" class="form-label">Actualizar Estado</label>
                    <select class="form-control" id="status" name="status">
                        <option value="abierto" <?php echo ($order['status'] ?? '') == 'abierto' ? 'selected' : ''; ?>>Abierto</option>
                        <option value="en_progreso" <?php echo ($order['status'] ?? '') == 'en_progreso' ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="cerrado" <?php echo ($order['status'] ?? '') == 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                        <option value="cancelado" <?php echo ($order['status'] ?? '') == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status_notes" class="form-label">Notas de actualización</label>
                    <textarea class="form-control" id="status_notes" name="status_notes" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
            </form>
            <?php if ($isClosed): ?>
                <a href="<?php echo $whatsappLink; ?>" class="btn btn-success mt-3" target="_blank">
                    <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Historial de Estados</h3>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_status_history ?? [] as $history): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($history['changed_at'] ?? 'now')); ?></td>
                        <td><?php echo htmlspecialchars($history['status'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($history['notes'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h3>Notas e Imágenes</h3>
            
            <?php if (!empty($order_notes['images'])): ?>
            <div class="card mb-4">
                <div class="card-header">Todas las Imágenes</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        <?php foreach ($order_notes['images'] as $image): ?>
                            <div class="mr-2 mb-2">
                                <a href="<?php echo url($image['path']); ?>" target="_blank">
                                    <img src="<?php echo url($image['path']); ?>" alt="Imagen de la orden" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </a>
                                <small class="d-block text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($image['created_at'])); ?>
                                    por <?php echo htmlspecialchars($image['user_name']); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php foreach ($order_notes['textNotes'] as $note): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($note['note'])); ?></p>
                    <p class="card-text"><small class="text-muted">
                        Por: <?php echo htmlspecialchars($note['user_name']); ?> 
                        el <?php echo date('d/m/Y H:i', strtotime($note['created_at'])); ?>
                    </small></p>
                </div>
            </div>
            <?php endforeach; ?>

            <form action="<?php echo url('services.php?action=add_note&id=' . ($order['id'] ?? '')); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="note" class="form-label">Agregar nota</label>
                    <textarea class="form-control" id="note" name="note" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="note_images" class="form-label">Agregar imágenes (opcional)</label>
                    <input type="file" class="form-control" id="note_images" name="note_images[]" accept="image/*" multiple>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Nota</button>
            </form>
        </div>
    </div>

    <h3 class="mt-4">Piezas Utilizadas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Número de Parte</th>
                <th>Cantidad</th>
                <th>Costo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_parts ?? [] as $part): ?>
            <tr>
                <td><?php echo htmlspecialchars($part['part_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($part['part_number'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($part['quantity'] ?? ''); ?></td>
                <td><?php echo number_format($part['cost'] ?? 0, 2); ?> ARS</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="<?php echo url('services.php?action=add_part&id=' . ($order['id'] ?? '')); ?>" method="post" class="mt-3">
        <h4>Agregar Pieza</h4>
        <div class="row">
            <div class="col-md-3 mb-3">
                <input type="text" class="form-control" name="part_name" placeholder="Nombre de la pieza" required>
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" class="form-control" name="part_number" placeholder="Número de parte">
            </div>
            <div class="col-md-2 mb-3">
                <input type="number" class="form-control" name="quantity" placeholder="Cantidad" required>
            </div>
            <div class="col-md-2 mb-3">
                <input type="number" step="0.01" class="form-control" name="cost" placeholder="Costo" required>
            </div>
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-primary">Agregar Pieza</button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <a href="<?php echo url('services.php?action=print&id=' . ($order['id'] ?? '')); ?>" class="btn btn-secondary" target="_blank">Imprimir Orden</a>
        <a href="<?php echo url('services.php'); ?>" class="btn btn-primary">Volver a la Lista</a>
    </div>
</div>

<script>
function printOrder(orderId) {
    window.open('<?php echo url("views/services/print.php"); ?>?id=' + orderId, '_blank');
}
</script>