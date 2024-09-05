<?php
function generateSalesReport($startDate, $endDate) {
    global $pdo;
    
    // Ajustamos la fecha de fin para incluir todo el día
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    
    // Obtener resumen de ventas
    $summaryQuery = "SELECT 
                        COUNT(*) as number_of_sales, 
                        COALESCE(SUM(total_amount), 0) as total_sales,
                        COALESCE(AVG(total_amount), 0) as average_sale,
                        COALESCE(MAX(total_amount), 0) as highest_sale
                     FROM sales
                     WHERE sale_date >= :start_date AND sale_date < :end_date";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Obtener detalles de ventas
    $salesQuery = "SELECT s.id, s.sale_date, c.name as customer_name, s.total_amount, s.payment_method, s.status
                   FROM sales s
                   LEFT JOIN customers c ON s.customer_id = c.id
                   WHERE s.sale_date >= :start_date AND s.sale_date < :end_date
                   ORDER BY s.sale_date DESC";
    $salesStmt = $pdo->prepare($salesQuery);
    $salesStmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    $sales = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener ventas por método de pago
    $paymentMethodQuery = "SELECT 
                             COALESCE(payment_method, 'Desconocido') as payment_method, 
                             COUNT(*) as count, 
                             COALESCE(SUM(total_amount), 0) as total
                           FROM sales
                           WHERE sale_date >= :start_date AND sale_date < :end_date
                           GROUP BY payment_method";
    $paymentMethodStmt = $pdo->prepare($paymentMethodQuery);
    $paymentMethodStmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    $paymentMethods = $paymentMethodStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'summary' => $summary,
        'sales' => $sales,
        'payment_methods' => $paymentMethods
    ];
}

function generateInventoryReport($days = 30) {
    global $pdo;
    
    // Obtener resumen del inventario
    $summaryQuery = "SELECT 
                        COUNT(*) as total_products,
                        SUM(stock_quantity * price) as total_value,
                        SUM(CASE WHEN stock_quantity <= reorder_level THEN 1 ELSE 0 END) as low_stock_count
                     FROM products";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Obtener detalles del inventario
    $inventoryQuery = "SELECT p.id, p.name, p.sku, c.name as category_name, p.stock_quantity, p.reorder_level, (p.stock_quantity * p.price) as stock_value
                       FROM products p
                       LEFT JOIN categories c ON p.category_id = c.id
                       ORDER BY p.stock_quantity ASC";
    $inventoryStmt = $pdo->prepare($inventoryQuery);
    $inventoryStmt->execute();
    $inventory = $inventoryStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener movimientos recientes de inventario
    $movementsQuery = "SELECT sm.created_at, p.name as product_name, sm.movement_type, sm.quantity, u.name as user_name
                       FROM stock_movements sm
                       JOIN products p ON sm.product_id = p.id
                       JOIN users u ON sm.user_id = u.id
                       WHERE sm.created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                       ORDER BY sm.created_at DESC";
    $movementsStmt = $pdo->prepare($movementsQuery);
    $movementsStmt->execute([':days' => $days]);
    $movements = $movementsStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'summary' => $summary,
        'inventory' => $inventory,
        'movements' => $movements
    ];
}

function generateAccountsReceivableReport() {
    global $pdo;
    $query = "SELECT 
                ca.id as account_id,
                c.id as customer_id, 
                c.name as customer_name, 
                ca.total_amount,
                ca.down_payment,
                ca.balance,
                ca.num_installments,
                ca.first_due_date,
                ca.last_payment_date,
                ca.status,
                (SELECT COUNT(*) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as pending_installments,
                (SELECT MIN(i.due_date) FROM installments i WHERE i.account_id = ca.id AND i.status != 'pagada') as next_due_date
              FROM customer_accounts ca
              JOIN customers c ON ca.customer_id = c.id
              WHERE ca.balance > 0
              ORDER BY ca.balance DESC, ca.total_amount DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generateCustomersReport($startDate, $endDate) {
    global $pdo;
    
    // Ajustar la fecha de fin para incluir todo el día
    $adjustedEndDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    
    // Obtener resumen de clientes
    $summaryQuery = "SELECT 
                        COUNT(DISTINCT c.id) as total_customers,
                        COUNT(DISTINCT s.customer_id) as active_customers,
                        COALESCE(SUM(s.total_amount), 0) as total_sales,
                        COALESCE(AVG(s.total_amount), 0) as average_per_customer
                     FROM customers c
                     LEFT JOIN sales s ON c.id = s.customer_id AND s.sale_date >= :start_date AND s.sale_date < :end_date";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Obtener top 10 clientes
    $topCustomersQuery = "SELECT c.id, c.name, c.email, COUNT(s.id) as total_purchases, 
                          COALESCE(SUM(s.total_amount), 0) as total_amount, 
                          MAX(s.sale_date) as last_purchase_date
                          FROM customers c
                          LEFT JOIN sales s ON c.id = s.customer_id AND s.sale_date >= :start_date AND s.sale_date < :end_date
                          GROUP BY c.id
                          ORDER BY total_amount DESC
                          LIMIT 10";
    $topCustomersStmt = $pdo->prepare($topCustomersQuery);
    $topCustomersStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $topCustomers = $topCustomersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener distribución de clientes por monto de compra
    $distributionQuery = "SELECT 
                            CASE 
                                WHEN total_amount = 0 THEN '0'
                                WHEN total_amount < 100 THEN '1-99'
                                WHEN total_amount < 500 THEN '100-499'
                                WHEN total_amount < 1000 THEN '500-999'
                                ELSE '1000+' 
                            END as purchase_range,
                            COUNT(*) as customer_count,
                            COUNT(*) * 100.0 / (SELECT COUNT(*) FROM customers) as percentage
                          FROM (
                            SELECT c.id, COALESCE(SUM(s.total_amount), 0) as total_amount
                            FROM customers c
                            LEFT JOIN sales s ON c.id = s.customer_id AND s.sale_date >= :start_date AND s.sale_date < :end_date
                            GROUP BY c.id
                          ) as customer_totals
                          GROUP BY purchase_range
                          ORDER BY 
                            CASE purchase_range
                                WHEN '0' THEN 1
                                WHEN '1-99' THEN 2
                                WHEN '100-499' THEN 3
                                WHEN '500-999' THEN 4
                                ELSE 5
                            END";
    $distributionStmt = $pdo->prepare($distributionQuery);
    $distributionStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $distribution = $distributionStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener clientes inactivos
    $inactiveCustomersQuery = "SELECT c.id, c.name, c.email, c.phone, MAX(s.sale_date) as last_purchase_date
                               FROM customers c
                               LEFT JOIN sales s ON c.id = s.customer_id
                               WHERE c.id NOT IN (
                                   SELECT DISTINCT customer_id 
                                   FROM sales 
                                   WHERE sale_date >= :start_date AND sale_date < :end_date
                               )
                               GROUP BY c.id
                               ORDER BY last_purchase_date DESC, c.id DESC";
    $inactiveCustomersStmt = $pdo->prepare($inactiveCustomersQuery);
    $inactiveCustomersStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $inactiveCustomers = $inactiveCustomersStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'summary' => $summary,
        'top_customers' => $topCustomers,
        'customer_distribution' => $distribution,
        'inactive_customers' => $inactiveCustomers
    ];
}


function generateCashRegisterReport($startDate, $endDate) {
    global $pdo;
    
    // Ajustamos la fecha de fin para incluir todo el día
    $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    
    // Obtener las sesiones de caja en el rango de fechas
    $sessionsQuery = "SELECT * FROM cash_register_sessions 
                      WHERE opening_date >= :start_date AND opening_date < :end_date
                      ORDER BY opening_date ASC";
    $sessionsStmt = $pdo->prepare($sessionsQuery);
    $sessionsStmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    $sessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $report = [];
    foreach ($sessions as $session) {
        $sessionReport = [
            'session' => $session,
            'movements' => [],
            'totals' => [
                'sales' => 0,
                'purchases' => 0,
                'cash_in' => 0,
                'cash_out' => 0
            ]
        ];

        // Determinar la fecha de cierre de la sesión
        $sessionEndDate = $session['closing_date'] ?? $endDate;

        // Obtener los movimientos de esta sesión
        $movementsQuery = "SELECT * FROM cash_register_movements 
                           WHERE created_at >= :start_date AND created_at < :end_date 
                           ORDER BY created_at ASC";
        $movementsStmt = $pdo->prepare($movementsQuery);
        $movementsStmt->execute([
            ':start_date' => $session['opening_date'],
            ':end_date' => $sessionEndDate
        ]);
        $movements = $movementsStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($movements as $movement) {
            $sessionReport['movements'][] = $movement;
            if (isset($movement['movement_type'])) {
                $sessionReport['totals'][$movement['movement_type']] += $movement['amount'];
            }
        }

        // Obtener las ventas de esta sesión
        $salesQuery = "SELECT SUM(total_amount) as total_sales FROM sales 
                       WHERE sale_date >= :start_date AND sale_date < :end_date";
        $salesStmt = $pdo->prepare($salesQuery);
        $salesStmt->execute([
            ':start_date' => $session['opening_date'],
            ':end_date' => $sessionEndDate
        ]);
        $salesResult = $salesStmt->fetch(PDO::FETCH_ASSOC);
        $sessionReport['totals']['sales'] = $salesResult['total_sales'] ?? 0;

        // Obtener las compras de esta sesión
        $purchasesQuery = "SELECT SUM(total_amount) as total_purchases FROM purchases 
                           WHERE purchase_date >= :start_date AND purchase_date < :end_date";
        $purchasesStmt = $pdo->prepare($purchasesQuery);
        $purchasesStmt->execute([
            ':start_date' => $session['opening_date'],
            ':end_date' => $sessionEndDate
        ]);
        $purchasesResult = $purchasesStmt->fetch(PDO::FETCH_ASSOC);
        $sessionReport['totals']['purchases'] = $purchasesResult['total_purchases'] ?? 0;

        $report[] = $sessionReport;
    }

    return $report;
}

function generatePurchasesReport($startDate, $endDate) {
    global $pdo;
    
    // Ajustar la fecha de fin para incluir todo el día
    $adjustedEndDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    
    // Obtener resumen de compras
    $summaryQuery = "SELECT 
                        COUNT(*) as number_of_purchases, 
                        COALESCE(SUM(total_amount), 0) as total_purchases,
                        COALESCE(AVG(total_amount), 0) as average_purchase,
                        COALESCE(MAX(total_amount), 0) as highest_purchase
                     FROM purchases
                     WHERE purchase_date >= :start_date AND purchase_date < :end_date";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Obtener detalles de compras
    $purchasesQuery = "SELECT p.id, p.purchase_date, s.name as supplier_name, p.total_amount, p.status
                       FROM purchases p
                       LEFT JOIN suppliers s ON p.supplier_id = s.id
                       WHERE p.purchase_date >= :start_date AND p.purchase_date < :end_date
                       ORDER BY p.purchase_date DESC";
    $purchasesStmt = $pdo->prepare($purchasesQuery);
    $purchasesStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $purchases = $purchasesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener top 10 productos más comprados
    $topProductsQuery = "SELECT p.name as product_name, SUM(pi.quantity) as total_quantity, SUM(pi.quantity * pi.price) as total_amount
                         FROM purchase_items pi
                         JOIN products p ON pi.product_id = p.id
                         JOIN purchases pu ON pi.purchase_id = pu.id
                         WHERE pu.purchase_date >= :start_date AND pu.purchase_date < :end_date
                         GROUP BY pi.product_id
                         ORDER BY total_quantity DESC
                         LIMIT 10";
    $topProductsStmt = $pdo->prepare($topProductsQuery);
    $topProductsStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'summary' => $summary,
        'purchases' => $purchases,
        'top_products' => $topProducts
    ];
}

function generateServiceReport($startDate, $endDate) {
    global $pdo;
    
    // Ajustar la fecha de fin para incluir todo el día
    $adjustedEndDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
    
    // Obtener resumen de órdenes de servicio
    $summaryQuery = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(CASE WHEN status = 'abierto' THEN 1 ELSE 0 END) as open_orders,
                        SUM(CASE WHEN status = 'en_progreso' THEN 1 ELSE 0 END) as in_progress_orders,
                        SUM(CASE WHEN status = 'cerrado' THEN 1 ELSE 0 END) as closed_orders,
                        SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelled_orders,
                        AVG(DATEDIFF(IFNULL(updated_at, CURRENT_TIMESTAMP), created_at)) as avg_resolution_time,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_order_value
                     FROM service_orders
                     WHERE created_at >= :start_date AND created_at < :end_date";
    $summaryStmt = $pdo->prepare($summaryQuery);
    $summaryStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // Obtener detalles de las órdenes de servicio
    $ordersQuery = "SELECT so.id, so.order_number, c.name as customer_name, so.status, 
                           so.created_at, so.updated_at, so.total_amount, so.prepaid_amount,
                           sd.brand, sd.model
                    FROM service_orders so
                    LEFT JOIN customers c ON so.customer_id = c.id
                    LEFT JOIN service_devices sd ON so.id = sd.service_order_id
                    WHERE so.created_at >= :start_date AND so.created_at < :end_date
                    ORDER BY so.created_at DESC";
    $ordersStmt = $pdo->prepare($ordersQuery);
    $ordersStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los servicios más comunes
    $topServicesQuery = "SELECT si.description, COUNT(*) as count, SUM(si.cost) as total_revenue
                         FROM service_items si
                         JOIN service_orders so ON si.service_order_id = so.id
                         WHERE so.created_at >= :start_date AND so.created_at < :end_date
                         GROUP BY si.description
                         ORDER BY count DESC
                         LIMIT 10";
    $topServicesStmt = $pdo->prepare($topServicesQuery);
    $topServicesStmt->execute([':start_date' => $startDate, ':end_date' => $adjustedEndDate]);
    $topServices = $topServicesStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'summary' => $summary,
        'orders' => $orders,
        'top_services' => $topServices
    ];
}

function generateProfitReport($startDate = null, $endDate = null) {
    global $pdo;
    
    $dateCondition = "";
    if ($startDate && $endDate) {
        $dateCondition = " WHERE sale_date BETWEEN :start_date AND :end_date";
    }

    // Obtener el total de ventas
    $salesQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_sales FROM sales" . $dateCondition;
    $salesStmt = $pdo->prepare($salesQuery);
    if ($startDate && $endDate) {
        $salesStmt->bindParam(':start_date', $startDate);
        $salesStmt->bindParam(':end_date', $endDate);
    }
    $salesStmt->execute();
    $totalSales = $salesStmt->fetchColumn();

    // Obtener el total de ingresos en efectivo
    $cashInQuery = "SELECT COALESCE(SUM(amount), 0) as total_cash_in 
                    FROM cash_register_movements 
                    WHERE movement_type = 'cash_in'" . 
                    ($dateCondition ? " AND" . substr($dateCondition, 6) : "");
    $cashInStmt = $pdo->prepare($cashInQuery);
    if ($startDate && $endDate) {
        $cashInStmt->bindParam(':start_date', $startDate);
        $cashInStmt->bindParam(':end_date', $endDate);
    }
    $cashInStmt->execute();
    $totalCashIn = $cashInStmt->fetchColumn();

    // Obtener el total de compras
    $purchasesQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_purchases FROM purchases" . 
                      ($dateCondition ? str_replace("sale_date", "purchase_date", $dateCondition) : "");
    $purchasesStmt = $pdo->prepare($purchasesQuery);
    if ($startDate && $endDate) {
        $purchasesStmt->bindParam(':start_date', $startDate);
        $purchasesStmt->bindParam(':end_date', $endDate);
    }
    $purchasesStmt->execute();
    $totalPurchases = $purchasesStmt->fetchColumn();

    // Calcular la ganancia
    $profit = $totalSales + $totalCashIn - $totalPurchases;

    return [
        'profit' => $profit,
        'total_sales' => $totalSales,
        'total_cash_in' => $totalCashIn,
        'total_purchases' => $totalPurchases,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];
}
?>