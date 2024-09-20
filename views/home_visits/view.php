<?php $pageTitle = "Detalles de Visita a Domicilio"; ?>
<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/utils.php';

// Obtener la información de la empresa
$companyInfo = getCompanyInfo();
?>

<div class="container mt-4">
    <h1>Detalles de Visita a Domicilio</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Visita #<?php echo $visit['id']; ?></h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($visit['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo $visit['visit_date']; ?></p>
            <p><strong>Hora:</strong> <?php echo $visit['visit_time']; ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($visit['status']); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($visit['notes'])); ?></p>
            
            <?php if ($visit['status'] === 'completada' && !empty($visit['customer_phone'])): ?>
                <?php
                $googleMapsUrl = !empty($companyInfo['google_maps_url']) ? $companyInfo['google_maps_url'] : '#';
                $message = urlencode("*La visita programada se realizó con éxito*. Por favor calificá nuestra atención dejando una Reseña aquí " . $googleMapsUrl);
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $visit['customer_phone']) . "?text=" . $message;
                ?>
                <a href="<?php echo $whatsappUrl; ?>" class="btn btn-success" target="_blank">
                    Enviar WhatsApp al Cliente
                </a>
            <?php endif; ?>
        </div>
    </div>
    <a href="home_visits.php" class="btn btn-secondary mt-3">Volver a la lista</a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>