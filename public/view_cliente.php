<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/home_visit_functions.php';
require_once __DIR__ . '/../includes/utils.php';

// Verificar si se proporcionó un token válido
$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Acceso no autorizado");
}

// Obtener la visita
$visit = getHomeVisitByToken($token);

if (!$visit) {
    die("Visita no encontrada");
}

// Obtener la información de la empresa
$companyInfo = getCompanyInfo();

$pageTitle = "Detalles de su Visita a Domicilio";
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
                <h5 class="card-title">Visita para <?php echo htmlspecialchars($visit['customer_name']); ?></h5>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?></p>
                <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($visit['visit_time'])); ?></p>
                <p><strong>Estado:</strong> <?php echo ucfirst(htmlspecialchars($visit['status'])); ?></p>
                <?php if (!empty($visit['notes'])): ?>
                    <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($visit['notes'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($visit['status'] == 'programada'): ?>
            <div class="mt-3">
                <h4>¿Necesita hacer cambios?</h4>
                <p>Si necesita reprogramar o cancelar su visita, por favor contáctenos:</p>
                <a href="tel:+<?php echo $companyInfo['phone']; ?>" class="btn btn-primary">Llamar</a>
                <?php 
                $whatsappMessage = urlencode("Hola, necesito hacer cambios en mi visita programada para el " . date('d/m/Y', strtotime($visit['visit_date'])) . " a las " . date('H:i', strtotime($visit['visit_time'])) . ".");
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