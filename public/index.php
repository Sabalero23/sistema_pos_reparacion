<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}

// Verificar si el usuario está autenticado
if (!isLoggedIn()) {
    header('Location: ' . url('login.php'));
    exit;
}

$pageTitle = "Dashboard - Sistema POS";
require_once __DIR__ . '/../includes/header.php';

function getTodaySalesTotal() {
    global $pdo;
    $query = "SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = CURDATE()";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getTodayIncome() {
    global $pdo;
    $query = "SELECT SUM(amount) as total FROM cash_register_movements WHERE DATE(created_at) = CURDATE() AND movement_type = 'cash_in'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// Obtener estadísticas básicas
$totalSales = getTotalSales();
$lowStockProducts = getLowStockProducts();
$recentSales = getRecentSales(5);
$todaySalesCount = getTodaySalesCount();
$todaySalesTotal = getTodaySalesTotal();
$todayIncome = getTodayIncome();

// Obtener datos para los gráficos
$salesByDayOfWeek = getSalesByDayOfWeek();
$topSellingProducts = getTopSellingProducts(5);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="content-wrapper mx-4">
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <h1 class="h3 mb-0">Dashboard</h1>
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="d-flex flex-wrap justify-content-md-end">
                            <?php if (hasPermission('services_view')): ?>
                                <a href="<?php echo url('services.php'); ?>" class="btn btn-warning m-1"><i class="fas fa-tools me-2"></i>Órdenes de Servicio</a>
                            <?php endif; ?>
                            <?php if (hasPermission('sales_create')): ?>
                                <a href="<?php echo url('pos.php'); ?>" class="btn btn-primary m-1"><i class="fas fa-cash-register me-2"></i>POS</a>
                            <?php endif; ?>
                            <?php if (hasPermission('products_view')): ?>
                                <a href="<?php echo url('products.php'); ?>" class="btn btn-secondary m-1"><i class="fas fa-box me-2"></i>Productos</a>
                            <?php endif; ?>
                            <?php if (hasPermission('customers_view')): ?>
                                <a href="<?php echo url('customers.php'); ?>" class="btn btn-info m-1"><i class="fas fa-users me-2"></i>Clientes</a>
                            <?php endif; ?>
                            <?php if (hasPermission('reports_view')): ?>
                                <a href="<?php echo url('reports.php'); ?>" class="btn btn-success m-1"><i class="fas fa-chart-bar me-2"></i>Reportes</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-primary">
                <div class="card-body text-primary">
                    <h5 class="card-title">Ventas e Ingresos del Mes</h5>
                    <p class="card-text fs-3 fw-bold">$<?php echo number_format(getTotalSales(), 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-success">
                <div class="card-body text-success">
                    <h5 class="card-title">Ingresos Hoy</h5>
                    <p class="card-text fs-3 fw-bold">$<?php echo number_format($todayIncome, 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-info">
                <div class="card-body text-info">
                    <h5 class="card-title">Ventas Hoy</h5>
                    <p class="card-text fs-3 fw-bold">$<?php echo number_format($todaySalesTotal, 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-warning">
                <div class="card-body text-warning">
                    <h5 class="card-title">Productos con Bajo Stock</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $lowStockProducts; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-bar me-2"></i>Ventas por Día de la Semana
                </div>
                <div class="card-body">
                    <canvas id="salesByDayChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-pie me-2"></i>Productos Más Vendidos
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-shopping-cart me-2"></i>Ventas Recientes
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td><?php echo $sale['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($sale['sale_date'])); ?></td>
                                    <td class="text-end">$<?php echo number_format($sale['total_amount'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        $lowStockProductsList = getLowStockProductsList(5);
        ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Productos con Bajo Stock (5 de <?php echo $lowStockProducts; ?>)
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Stock Actual</th>
                                <th class="text-center">Stock Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProductsList as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="text-center"><?php echo $product['stock_quantity']; ?></td>
                                    <td class="text-center"><?php echo $product['reorder_level']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Función para traducir los días a español
function translateDayToSpanish(day) {
    const daysInSpanish = {
        'Monday': 'Lunes',
        'Tuesday': 'Martes',
        'Wednesday': 'Miércoles',
        'Thursday': 'Jueves',
        'Friday': 'Viernes',
        'Saturday': 'Sábado',
        'Sunday': 'Domingo'
    };
    return daysInSpanish[day] || day;
}

// Datos para los gráficos
const salesByDay = <?php echo json_encode(array_values($salesByDayOfWeek)); ?>;
const daysOfWeek = <?php echo json_encode(array_keys($salesByDayOfWeek)); ?>;
const daysOfWeekInSpanish = daysOfWeek.map(translateDayToSpanish);
const topProducts = <?php echo json_encode(array_column($topSellingProducts, 'name')); ?>;
const topProductsSales = <?php echo json_encode(array_column($topSellingProducts, 'total_sold')); ?>;

// Gráfico de ventas por día de la semana
const salesByDayChart = new Chart(document.getElementById('salesByDayChart'), {
    type: 'bar',
    data: {
        labels: daysOfWeekInSpanish,
        datasets: [{
            label: 'Ventas',
            data: salesByDay,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Gráfico de productos más vendidos
const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
    type: 'pie',
    data: {
        labels: topProducts,
        datasets: [{
            data: topProductsSales,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            }
        }
    }
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';

// Funciones para obtener datos
function getTotalSales() {
    global $pdo;
    $query = "SELECT COALESCE(SUM(total_amount), 0) 
              FROM sales 
              WHERE YEAR(sale_date) = YEAR(CURDATE()) 
              AND MONTH(sale_date) = MONTH(CURDATE())";
    $stmt = $pdo->query($query);
    $salesTotal = $stmt->fetchColumn();

    // Obtener el total de ingresos de dinero del mes actual
    $incomesTotal = getTotalIncomes();

    // Sumar ventas e ingresos
    return $salesTotal + $incomesTotal;
}

function getTotalIncomes() {
    global $pdo;
    $query = "SELECT COALESCE(SUM(amount), 0) 
              FROM cash_register_movements 
              WHERE movement_type = 'cash_in' 
              AND YEAR(created_at) = YEAR(CURDATE()) 
              AND MONTH(created_at) = MONTH(CURDATE())";
    $stmt = $pdo->query($query);
    return $stmt->fetchColumn();
}

function getLowStockProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= reorder_level");
    return $stmt->fetchColumn();
}

function getLowStockProductsList($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE stock_quantity <= reorder_level ORDER BY stock_quantity ASC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRecentSales($limit) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM sales ORDER BY sale_date DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTodaySalesCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM sales WHERE DATE(sale_date) = CURDATE()");
    return $stmt->fetchColumn();
}

function getSalesByDayOfWeek() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT 
            DAYNAME(sale_date) as day_name, 
            SUM(total_amount) as total_sales
        FROM sales
        WHERE 
            YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY DAYNAME(sale_date)
        ORDER BY FIELD(DAYNAME(sale_date), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function getTopSellingProducts($limit) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            p.name, 
            SUM(si.quantity) as total_sold
        FROM sale_items si
        JOIN products p ON si.product_id = p.id
        GROUP BY si.product_id
        ORDER BY total_sold DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>