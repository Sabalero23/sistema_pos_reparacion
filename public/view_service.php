<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/remote_service_functions.php';
require_once __DIR__ . '/../includes/utils.php';

// Verificar si se proporcionó un token válido
$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Acceso no autorizado");
}

// Obtener el servicio remoto
$service = getRemoteServiceByToken($token);

if (!$service) {
    die("Servicio remoto no encontrado");
}

// Obtener la información de la empresa
$companyInfo = getCompanyInfo();

$pageTitle = "Detalles de su Servicio Remoto";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?php echo $pageTitle; ?></h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Servicio Remoto para <?php echo htmlspecialchars($service['customer_name'] ?? ''); ?></h5>
                <p><strong>Número de Servicio:</strong> <?php echo htmlspecialchars($service['service_number'] ?? ''); ?></p>
                <p><strong>Fecha Programada:</strong> <?php echo date('d/m/Y', strtotime($service['service_date'] ?? '')); ?></p>
                <p><strong>Hora Programada:</strong> <?php echo date('H:i', strtotime($service['service_time'] ?? '')); ?></p>
                <p><strong>Estado:</strong> <?php echo ucfirst(htmlspecialchars($service['status'] ?? '')); ?></p>
                <?php if (!empty($service['notes'])): ?>
                    <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($service['notes'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (($service['status'] ?? '') == 'programado'): ?>
            <div class="mt-3">
                <h4>¿Necesita hacer cambios?</h4>
                <p>Si necesita reprogramar o cancelar su servicio remoto, por favor contáctenos:</p>
                <a href="tel:+<?php echo $companyInfo['phone']; ?>" class="btn btn-primary">Llamar</a>
                <?php 
                $serviceNumber = $service['service_number'] ?? '';
                $whatsappMessage = urlencode("Hola, necesito hacer cambios en mi servicio remoto #" . $serviceNumber . " programado para el " . date('d/m/Y', strtotime($service['service_date'] ?? '')) . " a las " . date('H:i', strtotime($service['service_time'] ?? '')) . ".");
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $companyInfo['phone']) . "?text=" . $whatsappMessage;
                ?>
                <a href="<?php echo $whatsappUrl; ?>" class="btn btn-success" target="_blank">WhatsApp</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>