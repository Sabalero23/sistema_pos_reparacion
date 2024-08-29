<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/utils.php';

if (!isLoggedIn() || !hasPermission('settings_view')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
}

$companyInfo = getCompanyInfo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('Token CSRF no válido. Por favor inténtalo de nuevo.', 'danger');
        redirect('company_settings.php');
    }

    $companyData = [
        'name' => sanitizeInput($_POST['name']),
        'address' => sanitizeInput($_POST['address']),
        'phone' => sanitizeInput($_POST['phone']),
        'email' => sanitizeInput($_POST['email']),
        'website' => sanitizeInput($_POST['website']),
        'legal_info' => sanitizeInput($_POST['legal_info']),
        'receipt_footer' => sanitizeInput($_POST['receipt_footer'])
    ];

// Manejar la carga del nuevo logo
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $uploadDir = __DIR__ . '/../public/uploads/';
    $logoPath = handleFileUpload($_FILES['logo'], $allowedExtensions, $uploadDir);
    if ($logoPath) {
        // Asegurarse de que la ruta comienza con '/public'
        $companyData['logo_path'] = '/public' . $logoPath;
        // Eliminar el logo anterior si existe
        if (!empty($companyInfo['logo_path'])) {
            deleteFile($companyInfo['logo_path']);
        }
    }
}

    if (updateCompanyInfo($companyData)) {
        setFlashMessage('Información de la empresa actualizada exitosamente', 'success');
    } else {
        setFlashMessage('Error al actualizar la información de la empresa', 'danger');
    }
    redirect('company_settings.php');
}

$pageTitle = "Configuración de la Empresa";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Configuración de la Empresa</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre de la Empresa</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($companyInfo['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($companyInfo['address']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($companyInfo['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($companyInfo['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="website" class="form-label">Sitio Web</label>
            <input type="url" name="website" id="website" class="form-control" value="<?php echo htmlspecialchars($companyInfo['website']); ?>">
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Logo de la Empresa</label>
            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
            <?php if (!empty($companyInfo['logo_path'])): ?>
    <img src="<?php echo htmlspecialchars($companyInfo['logo_path']); ?>" alt="Logo actual" class="mt-2" style="max-width: 200px; max-height: 50px;">
<?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="legal_info" class="form-label">Información Legal</label>
            <textarea name="legal_info" id="legal_info" class="form-control" rows="3"><?php echo htmlspecialchars($companyInfo['legal_info']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="receipt_footer" class="form-label">Pie de Página del Comprobante</label>
            <textarea name="receipt_footer" id="receipt_footer" class="form-control" rows="3"><?php echo htmlspecialchars($companyInfo['receipt_footer']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>