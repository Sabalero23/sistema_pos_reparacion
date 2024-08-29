<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


// Verificar si el usuario está autenticado y tiene permiso
if (!isLoggedIn() || !hasPermission('backup_create')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$pageTitle = "Gestión de Copias de Seguridad";  
require_once __DIR__ . '/../includes/header.php';

// Función para formatear bytes a unidades legibles
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow]; 
}

// Directorio para almacenar las copias de seguridad
$backupDir = __DIR__ . '/../config/backups/';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
    // Verificar el token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error de validación CSRF.");
    }

    // Generar nombre de archivo único para la copia de seguridad  
    $backupFileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $backupFilePath = $backupDir . $backupFileName;
    
    try {
        // Obtener todas las tablas
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        // Iniciar el archivo de respaldo
        $output = "-- Copia de seguridad de la base de datos " . DB_NAME . "\n";
        $output .= "-- Generada el " . date("Y-m-d H:i:s") . "\n\n";
                                       
        // Recorrer cada tabla
        foreach ($tables as $table) {
            $result = $pdo->query("SELECT * FROM `$table`");
            $numFields = $result->columnCount(); 
            
            $output .= "DROP TABLE IF EXISTS `$table`;\n";

            $row2 = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $output .= "\n\n".$row2[1].";\n\n";

            while ($row = $result->fetch(PDO::FETCH_NUM)) { 
                $output .= "INSERT INTO `$table` VALUES(";
                for ($j=0; $j < $numFields; $j++) { 
                    if (isset($row[$j])) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        $output .= '"'.$row[$j].'"' ; 
                    } else {
                        $output .= 'NULL';
                    }
                     
                    if ($j < ($numFields - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ");\n";
            }
            $output .= "\n\n\n";
        }
        
        // Guardar el archivo
        if (file_put_contents($backupFilePath, $output)) {
            $message = "Copia de seguridad creada con éxito: " . $backupFileName;
            $messageType = 'success';
        } else {
            throw new Exception("No se pudo escribir el archivo de respaldo.");
        }
    } catch (Exception $e) {
        $message = "Error al crear la copia de seguridad: " . $e->getMessage();
        $messageType = 'danger'; 
    }
}

// Obtener lista de copias de seguridad existentes
$backups = glob($backupDir . '*.sql');
rsort($backups); // Ordenar por fecha, más reciente primero

// Generar un nuevo token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container">
    <h1 class="my-4">Gestión de Copias de Seguridad</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            Acciones de Copias de Seguridad
        </div>
        <div class="card-body">
            <form method="post" action="" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="create_backup" class="btn btn-primary">Crear Copia de Seguridad</button>
            </form>
            <?php if (hasPermission('backup_restore')): ?>
                <a href="<?php echo url('restore.php'); ?>" class="btn btn-warning">Restaurar Copia de Seguridad</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            Copias de Seguridad Existentes  
        </div>
        <div class="card-body">
            <?php if (empty($backups)): ?>
                <p>No hay copias de seguridad disponibles.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre del Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><?php echo basename($backup); ?></td>
                                <td><?php echo formatBytes(filesize($backup)); ?></td>
                                <td><?php echo date("Y-m-d H:i:s", filemtime($backup)); ?></td>
                                <td>
                                    <a href="<?php echo url('download_backup.php?file=' . urlencode(basename($backup))); ?>" class="btn btn-sm btn-info">Descargar</a>
                                    <a href="<?php echo url('delete_backup.php?file=' . urlencode(basename($backup))); ?>" class="btn btn-sm btn-danger delete-backup">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmación para eliminar copia de seguridad  
    const deleteButtons = document.querySelectorAll('.delete-backup');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});  
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>