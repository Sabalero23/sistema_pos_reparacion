<?php
// Iniciar el almacenamiento en búfer de salida
ob_start();

// Habilitar el reporte de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función de logging
function debug_log($message) {
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

debug_log('Script started');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php'; // Añadido: incluye el archivo de roles
require_once __DIR__ . '/../../includes/home_visit_functions.php';

debug_log('Required files loaded');

header('Content-Type: application/json');

if (!function_exists('hasPermission')) {
    debug_log('hasPermission function not found');
    echo json_encode(['error' => 'Error de configuración del servidor.']);
    exit;
}

if (!isLoggedIn() || !hasPermission('calendar_view')) {
    debug_log('Authentication or permission check failed');
    echo json_encode(['error' => 'No tienes permiso para acceder a esta página.']);
    exit;
}

try {
    debug_log('Getting home visits');
    $visits = getAllHomeVisits();
    debug_log('Raw visits data: ' . print_r($visits, true));

    $events = array_map(function($visit) {
        return [
            'title' => htmlspecialchars($visit['customer_name'] ?? 'Cliente desconocido'),
            'start' => $visit['visit_date'] . 'T' . $visit['visit_time'],
            'url' => url('home_visits.php?action=view&id=' . $visit['id'])
        ];
    }, $visits);

    debug_log('Events array created');

    $json = json_encode($events, JSON_PRETTY_PRINT);
    
    if ($json === false) {
        throw new Exception('Failed to encode JSON: ' . json_last_error_msg());
    }
    
    debug_log('JSON encoded successfully');
    echo $json;
} catch (Exception $e) {
    debug_log('Error: ' . $e->getMessage());
    echo json_encode(['error' => 'Ocurrió un error al obtener los eventos: ' . $e->getMessage()]);
}

// Capturar cualquier salida inesperada
$output = ob_get_clean();
debug_log('Final output: ' . $output);

// Imprimir la respuesta JSON
echo $output;

debug_log('Script ended');
?>