<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Asegurarnos de que la zona horaria esté correctamente configurada
date_default_timezone_set(TIMEZONE);

// Función para formatear la fecha y hora
function formatDateTime($dateTimeString) {
    $dateTime = new DateTime($dateTimeString, new DateTimeZone(TIMEZONE));
    return $dateTime->format('Y-m-d H:i:s');
}

// Obtener las ventas de la base de datos
$sales = getAllSales(); // Asume que tienes una función que obtiene todas las ventas

// HTML para mostrar la lista de ventas
?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<style>
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .title-button-wrapper {
        display: flex;
        align-items: center;
        gap: 20px; /* Espacio entre el título y el botón */
    }
    .pos-button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
        font-size: 1rem;
        white-space: nowrap;
    }
    .pos-button:hover {
        background-color: #218838;
        color: white;
    }
</style>
</head>
<body>
    <div class="container mt-4">
        <div class="header-container mb-4">
            <div class="title-button-wrapper">
                <h1>Gestión de Ventas</h1>
                <a href="pos.php" class="pos-button">POS</a>
            </div>
        </div>
    
        <?php if (hasPermission('sales_create')): ?>
            <a href="<?php echo url('sales.php?action=create'); ?>" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Nueva Venta
            </a>
        <?php endif; ?>
        <h1>Listado de Ventas</h1>
        <table id="salesTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha y Hora</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo $sale['id']; ?></td>
                    <td><?php echo formatDateTime($sale['sale_date']); ?></td>
                    <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Consumidor Final'); ?></td>
                    <td><?php echo number_format($sale['total_amount'], 2) . ' ' . CURRENCY; ?></td>
                    <td><?php echo ucfirst($sale['payment_method']); ?></td>
                    <td><?php echo ucfirst($sale['status']); ?></td>
                    <td>
                        <a href="<?php echo url('sales.php?action=view&id=' . $sale['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[1, "desc"]], // Ordenar por fecha (columna 1) de forma descendente
            "pageLength": 25, // Mostrar 25 registros por página
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Desactivar ordenamiento para la columna de acciones
            ]
        });
    });
    </script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>