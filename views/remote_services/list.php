<?php $pageTitle = "Lista de Servicios Remotos"; ?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<?php
// Función para generar la URL de WhatsApp
function generateWhatsAppUrl($phoneNumber, $message, $serviceId, $accessToken) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $viewUrl = $baseUrl . "/public/view_service.php?token=" . urlencode($accessToken);
    
    // Creamos un mensaje corto para el enlace de WhatsApp
    $shortMessage = "Detalles de su servicio remoto: " . $viewUrl;
    
    // Codificamos el mensaje corto para la URL
    $encodedMessage = urlencode($shortMessage);
    
    // Generamos la URL de WhatsApp con el mensaje corto
    return "https://wa.me/" . preg_replace('/[^0-9]/', '', $phoneNumber) . "?text=" . $encodedMessage;
}
?>

<div class="container mt-4">
    <h1>Lista de Servicios Remotos</h1>

    <?php if (hasPermission('remote_services_create')): ?>
        <a href="<?php echo url('remote_services.php?action=create'); ?>" class="btn btn-primary mb-3">Nuevo Servicio Remoto</a>
    <?php endif; ?>

    <table class="table table-striped" id="remoteServicesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número de Servicio</th>
                <th>Cliente</th>
                <th>Técnico</th>
                <th>Fecha Programada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo $service['id']; ?></td>
                    <td><?php echo isset($service['service_number']) ? htmlspecialchars($service['service_number']) : ''; ?></td>
                    <td><?php echo isset($service['customer_name']) ? htmlspecialchars($service['customer_name']) : ''; ?></td>
                    <td><?php echo isset($service['technician_name']) ? htmlspecialchars($service['technician_name']) : ''; ?></td>
                    <td><?php echo isset($service['scheduled_date']) ? date('d/m/Y H:i', strtotime($service['scheduled_date'])) : ''; ?></td>
                    <td><?php echo isset($service['status']) ? ucfirst($service['status']) : ''; ?></td>
                    <td>
                        <a href="<?php echo url('remote_services.php?action=view&id=' . $service['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                        <?php if (hasPermission('remote_services_edit')): ?>
                            <a href="<?php echo url('remote_services.php?action=edit&id=' . $service['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                        <?php endif; ?>
                        <?php if (hasPermission('remote_services_delete')): ?>
                            <button class="btn btn-sm btn-danger delete-service" data-id="<?php echo $service['id']; ?>">Eliminar</button>
                        <?php endif; ?>
                        <?php
                        if (!empty($service['customer_phone']) && !empty($service['access_token']) && isset($service['scheduled_date'])): 
                            $message = "Hola " . (isset($service['customer_name']) ? $service['customer_name'] : '') . ", su servicio remoto ha sido programado para el " . date('d/m/Y H:i', strtotime($service['scheduled_date'])) . ".";
                            $whatsappUrl = generateWhatsAppUrl($service['customer_phone'], $message, $service['id'], $service['access_token']);
                        ?>
                        <a href="<?php echo $whatsappUrl; ?>" class="btn btn-sm btn-success send-whatsapp" 
                           data-fullmessage="<?php echo htmlspecialchars($message); ?>" target="_blank">
                            Enviar WhatsApp
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#remoteServicesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });

    $('.delete-service').on('click', function() {
        var serviceId = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo url("remote_services.php?action=delete&id="); ?>' + serviceId;
            }
        });
    });

    $('.send-whatsapp').on('click', function(e) {
        e.preventDefault();
        var whatsappUrl = $(this).attr('href');
        var fullMessage = $(this).data('fullmessage');
        
        // Abre WhatsApp Web o la aplicación móvil con el enlace corto
        window.open(whatsappUrl, '_blank');
        
        // Muestra un modal con el mensaje completo para copiar
        Swal.fire({
            title: 'Mensaje para WhatsApp',
            html: '<textarea class="form-control" rows="5" id="fullMessage" readonly>' + fullMessage + '</textarea>',
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'Copiar mensaje',
            cancelButtonText: 'Cerrar',
            preConfirm: () => {
                var messageElem = document.getElementById('fullMessage');
                messageElem.select();
                document.execCommand('copy');
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Copiado', 'El mensaje ha sido copiado al portapapeles', 'success');
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>