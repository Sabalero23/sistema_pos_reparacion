<?php
// Verificar si el usuario está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Lista de Servicios Remotos";

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

// Obtener todos los servicios remotos
$services = getAllRemoteServices();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Lista de Servicios Remotos</h1>
    <?php if (hasPermission('remote_services_create')): ?>
        <a href="<?php echo url('remote_services.php?action=create'); ?>" class="btn btn-primary mb-3">Nuevo Servicio</a>
    <?php endif; ?>
    
    <table id="remoteServicesTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['id']); ?></td>
                <td><?php echo htmlspecialchars($service['customer_name']); ?></td>
                <td data-order="<?php echo strtotime($service['service_date']); ?>">
                    <?php echo date('d/m/Y', strtotime($service['service_date'])); ?>
                </td>
                <td><?php echo date('H:i', strtotime($service['service_time'])); ?></td>
                <td>
                    <?php
                    $statusClass = '';
                    switch($service['status']) {
                        case 'programado':
                            $statusClass = 'badge bg-warning';
                            break;
                        case 'completado':
                            $statusClass = 'badge bg-success';
                            break;
                        case 'cancelado':
                            $statusClass = 'badge bg-danger';
                            break;
                        default:
                            $statusClass = 'badge bg-secondary';
                    }
                    ?>
                    <span class="<?php echo $statusClass; ?>"><?php echo ucfirst(htmlspecialchars($service['status'])); ?></span>
                </td>
                <td>
                    <a href="<?php echo url('remote_services.php?action=view&id=' . $service['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                    <?php if (hasPermission('remote_services_edit')): ?>
                        <a href="<?php echo url('remote_services.php?action=edit&id=' . $service['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                    <?php endif; ?>
                    <?php if (hasPermission('remote_services_delete')): ?>
                        <form action="<?php echo url('remote_services.php?action=delete&id=' . $service['id']); ?>" method="post" style="display:inline;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este servicio remoto?')">Eliminar</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!empty($service['customer_phone']) && !empty($service['access_token'])): 
                        $message = "Hola {$service['customer_name']}, su servicio remoto ha sido programado para el " . date('d/m/Y', strtotime($service['service_date'])) . " a las " . date('H:i', strtotime($service['service_time'])) . ".";
                        $whatsappUrl = generateWhatsAppUrl($service['customer_phone'], $message, $service['id'], $service['access_token']);
                    ?>
                    <a href="<?php echo $whatsappUrl; ?>" class="btn btn-sm btn-success" target="_blank">
                        Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>