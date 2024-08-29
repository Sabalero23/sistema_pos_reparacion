<?php
require_once __DIR__ . '/../config/config.php';  
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


// Verificar si el usuario está autenticado y tiene permiso
if (!isLoggedIn() || !hasPermission('backup_download')) {
    $_SESSION['flash_message'] = "No tienes permiso para descargar copias de seguridad.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

// Directorio de copias de seguridad
$backupDir = __DIR__ . '/../config/backups/';

// Obtener el nombre del archivo de la URL 
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

// Sanitizar el nombre del archivo
$file = preg_replace("/[^a-zA-Z0-9_\-\.]/", "", $file); 

$filePath = $backupDir . $file;

// Verificar que el archivo existe y es un archivo regular
if (!is_file($filePath)) {
    $_SESSION['flash_message'] = "Archivo no encontrado.";
    $_SESSION['flash_type'] = 'danger'; 
    header('Location: ' . url('backup.php'));
    exit;
}

// Verificar la extensión del archivo 
$fileInfo = pathinfo($filePath);
if (isset($fileInfo['extension']) && $fileInfo['extension'] !== 'sql') {
    $_SESSION['flash_message'] = "Tipo de archivo no permitido.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: ' . url('backup.php'));
    exit;  
}

// Configurar headers para la descarga
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($filePath).'"'); 
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Enviar el archivo y salir
readfile($filePath);
exit;