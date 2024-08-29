<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


// Verificar si el usuario está autenticado y tiene permiso
if (!isLoggedIn() || !hasPermission('backup_restore')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$pageTitle = "Restaurar Copia de Seguridad";
require_once __DIR__ . '/../includes/header.php';

// Directorio de copias de seguridad
$backupDir = __DIR__ . '/../config/backups/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_backup'])) {
    // Verificar el token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error de validación CSRF.");
    }

    $backupFile = $_POST['backup_file'];
    $backupFilePath = $backupDir . basename($backupFile);

    // Verificar que el archivo existe y está dentro del directorio de copias de seguridad
    if (!file_exists($backupFilePath) || dirname(realpath($backupFilePath)) !== $backupDir) {
        $_SESSION['flash_message'] = "Archivo de copia de seguridad no válido.";
        $_SESSION['flash_type'] = 'danger';
    } else {
        try {
            // Obtener los detalles de la conexión a la base de datos desde la configuración
            $dbHost = DB_HOST;
            $dbName = DB_NAME;
            $dbUser = DB_USER;
            $dbPassword = DB_PASS;

            // Comando para restaurar la copia de seguridad utilizando mysql
            $command = "mysql -h $dbHost -u $dbUser -p'$dbPassword' $dbName < $backupFilePath";

            // Ejecutar el comando
            exec($command, $output, $returnValue);

            if ($returnValue === 0) {
                $_SESSION['flash_message'] = "Copia de seguridad restaurada con éxito.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Error al restaurar la copia de seguridad. Código de error: $returnValue";
                $_SESSION['flash_type'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Error al restaurar la copia de seguridad: " . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    }
}

// Obtener lista de copias de seguridad disponibles
$backups = glob($backupDir . '*.sql');
rsort($backups); // Ordenar por fecha, más reciente primero

// Generar un nuevo token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container">
    <h1 class="my-4">Restaurar Copia de Seguridad</h1>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['flash_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            Seleccionar Copia de Seguridad para Restaurar
        </div>
        <div class="card-body">
            <?php if (empty($backups)): ?>
                <p>No hay copias de seguridad disponibles para restaurar.</p>
            <?php else: ?>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="backup_file" class="form-label">Seleccione una copia de seguridad:</label>
                        <select name="backup_file" id="backup_file" class="form-select" required>
                            <?php foreach ($backups as $backup): ?>
                                <option value="<?php echo htmlspecialchars(basename($backup)); ?>">
                                    <?php echo htmlspecialchars(basename($backup)) . ' - ' . date("Y-m-d H:i:s", filemtime($backup)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="confirm_restore" required>
                        <label class="form-check-label" for="confirm_restore">Confirmo que deseo restaurar esta copia de seguridad. Entiendo que esta acción sobrescribirá todos los datos actuales.</label>
                    </div>
                    <button type="submit" name="restore_backup" class="btn btn-warning" id="restoreButton" disabled>Restaurar Copia de Seguridad</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirm_restore');
    const restoreButton = document.getElementById('restoreButton');

    if (confirmCheckbox && restoreButton) {
        confirmCheckbox.addEventListener('change', function() {
            restoreButton.disabled = !this.checked;
        });

        restoreButton.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción sobrescribirá todos los datos actuales. ¿Deseas continuar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('form').submit();
                }
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>