<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/report_functions.php';


if (!isLoggedIn() || !hasPermission('reports_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$reportType = $_GET['type'] ?? '';

$pageTitle = "Reportes";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        include __DIR__ . '/../views/reports/list.php';
        break;
    case 'generate':
        if (!hasPermission('reports_generate')) {
            $_SESSION['flash_message'] = "No tienes permiso para generar reportes.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('reports.php'));
            exit;
        }
        
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        
        switch ($reportType) {
            case 'sales':
                if (empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_message'] = "Fechas de inicio y fin requeridas para el reporte de ventas.";
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . url('reports.php'));
                    exit;
                }
                $reportData = generateSalesReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/sales_report.php';
                break;
            case 'inventory':
                $days = isset($_POST['days']) ? intval($_POST['days']) : 30;
                $reportData = generateInventoryReport($days);
                include __DIR__ . '/../views/reports/inventory_report.php';
                break;
            case 'customers':
                if (empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_message'] = "Fechas de inicio y fin requeridas para el reporte de clientes.";
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . url('reports.php'));
                    exit;
                }
                $reportData = generateCustomersReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/customers_report.php';
                break;
            case 'accounts_receivable':
                $reportData = generateAccountsReceivableReport();
                include __DIR__ . '/../views/reports/accounts_receivable_report.php';
                break;
            case 'cash_register':
                if (empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_message'] = "Fechas de inicio y fin requeridas para el reporte de caja registradora.";
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . url('reports.php'));
                    exit;
                }
                $reportData = generateCashRegisterReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/cash_register_report.php';
                break;
            case 'purchases':
                if (empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_message'] = "Fechas de inicio y fin requeridas para el reporte de compras.";
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . url('reports.php'));
                    exit;
                }
                $reportData = generatePurchasesReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/purchases_report.php';
                break;
            case 'services':
                if (empty($startDate) || empty($endDate)) {
                    $_SESSION['flash_message'] = "Fechas de inicio y fin requeridas para el reporte de servicios.";
                    $_SESSION['flash_type'] = 'error';
                    header('Location: ' . url('reports.php'));
                    exit;
                }
                $reportData = generateServiceReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/services_report.php';
                break;
            // En reports.php, dentro del case 'profit':
            case 'profit':
                customLog("Generando reporte de ganancias. Start Date: $startDate, End Date: $endDate");
                
                // Asegurarse de que las fechas sean válidas
                if (!empty($startDate) && !empty($endDate)) {
                    $startDate = date('Y-m-d', strtotime($startDate));
                    $endDate = date('Y-m-d', strtotime($endDate));
                } else {
                    // Si no se proporcionan fechas, usar el mes actual
                    $startDate = date('Y-m-01'); // Primer día del mes actual
                    $endDate = date('Y-m-d'); // Día actual
                }
                
                $reportData = generateProfitReport($startDate, $endDate);
                include __DIR__ . '/../views/reports/profit_report.php';
                break;
            default:
                $_SESSION['flash_message'] = "Tipo de reporte no válido.";
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . url('reports.php'));
                exit;
        }
        break;
    default:
        $_SESSION['flash_message'] = "Acción no válida.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('reports.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>