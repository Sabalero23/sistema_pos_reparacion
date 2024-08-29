<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/utils.php';

if (!isLoggedIn() || !hasPermission('settings_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$settings = getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('Token CSRF no válido. Por favor inténtalo de nuevo.', 'danger');
        redirect('settings.php');
    }

    $settings = $_POST;

    // Manejar la carga del nuevo logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = __DIR__ . '/../public/uploads/';
            $newFilename = 'logo_' . time() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $newFilename;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                $settings['logo_path'] = '/uploads/' . $newFilename;
            } else {
                setFlashMessage('Error al subir el archivo.', 'danger');
            }
        } else {
            setFlashMessage('Tipo de archivo no permitido. Por favor, sube una imagen (jpg, jpeg, png, gif).', 'danger');
        }
    }

    updateSettings($settings);
    setFlashMessage('Configuración actualizada exitosamente', 'success');
    redirect('settings.php');
}

$pageTitle = "Configuración";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Configuración del Sistema</h1>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="mb-3">
            <label for="app_name" class="form-label">Nombre</label>
            <input type="text" name="app_name" id="app_name" class="form-control" value="<?php echo htmlspecialchars($settings['app_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="timezone" class="form-label">Zona Horaria</label>
            <select name="timezone" id="timezone" class="form-select" required>
                <?php foreach (DateTimeZone::listIdentifiers() as $timezone): ?>
                    <option value="<?php echo $timezone; ?>" <?php if ($settings['timezone'] === $timezone) echo 'selected'; ?>><?php echo $timezone; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="currency" class="form-label">Moneda</label>
            <input type="text" name="currency" id="currency" class="form-control" value="<?php echo htmlspecialchars($settings['currency']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="admin_email" class="form-label">Correo Administrativo</label>
            <input type="email" name="admin_email" id="admin_email" class="form-control" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="items_per_page" class="form-label">Artículos por página</label>
            <input type="number" name="items_per_page" id="items_per_page" class="form-control" value="<?php echo $settings['items_per_page']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="tax_rate" class="form-label">Tasa impositiva (%) (para 21% colocar 0,21)</label>
            <input type="number" name="tax_rate" id="tax_rate" class="form-control" min="0" max="100" step="0.01" value="<?php echo $settings['tax_rate']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Logo del Sistema</label>
            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
            <?php if (!empty($settings['logo_path'])): ?>
                <img src="<?php echo url($settings['logo_path']); ?>" alt="Logo actual" class="mt-2" style="max-width: 200px; max-height: 50px;">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>