<?php $pageTitle = "Detalles de Servicio Remoto"; ?>
<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../../includes/remote_service_functions.php';

// Verificar si se proporcionó un ID de servicio
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID de servicio no válido.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: remote_services.php');
    exit();
}

$serviceId = $_GET['id'];
$service = getRemoteService($serviceId);

// Verificar si el servicio existe
if (!$service) {
    $_SESSION['flash_message'] = "Servicio no encontrado.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: remote_services.php');
    exit();
}

// Obtener la información de la empresa
$companyInfo = getCompanyInfo();
?>

<div class="container mt-4">
    <h1>Detalles de Servicio Remoto</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Servicio #<?php echo htmlspecialchars($service['id']); ?></h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($service['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($service['service_date']); ?></p>
            <p><strong>Hora:</strong> <?php echo htmlspecialchars($service['service_time']); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst(htmlspecialchars($service['status'] ?? 'N/A')); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($service['notes'] ?? '')); ?></p>
            
            <?php if (($service['status'] ?? '') === 'completado' && !empty($service['customer_phone'])): ?>
                <?php
                $googleMapsUrl = !empty($companyInfo['google_maps_url']) ? $companyInfo['google_maps_url'] : '#';
                $message = urlencode("*El servicio remoto programado se realizó con éxito*. Por favor califica nuestra atención dejando una reseña aquí " . $googleMapsUrl);
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $service['customer_phone']) . "?text=" . $message;
                ?>
                <a href="<?php echo $whatsappUrl; ?>" class="btn btn-success" target="_blank">
                    Enviar WhatsApp al Cliente
                </a>
            <?php endif; ?>
        </div>
    </div>
    <a href="remote_services.php" class="btn btn-secondary mt-3">Volver a la lista</a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>