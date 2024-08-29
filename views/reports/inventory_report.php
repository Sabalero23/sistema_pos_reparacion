<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="mb-2">Reporte de Inventario</h1>
            <?php if (isset($days)): ?>
                <p class="mb-0">Movimientos de los últimos <?php echo htmlspecialchars($days); ?> días</p>
            <?php else: ?>
                <p class="mb-0">Reporte de inventario actual</p>
            <?php endif; ?>
        </div>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Productos</h5>
                    <p class="card-text"><?php echo $reportData['summary']['total_products']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Valor Total del Inventario</h5>
                    <p class="card-text"><?php echo number_format($reportData['summary']['total_value'], 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Productos con Stock Bajo</h5>
                    <p class="card-text"><?php echo $reportData['summary']['low_stock_count']; ?></p>
                </div>
            </div>
        </div>
    </div>

        <div id="printable-container">
        <div class="printable-section">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Estado Actual del Inventario</h3>
                <button onclick="window.print()" class="btn btn-primary">Imprimir Estado Actual del Inventario</button>
            </div>
            <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Nivel de Reorden</th>
                <th>Valor en Stock</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData['inventory'] as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['sku']; ?></td>
                <td><?php echo $product['category_name']; ?></td>
                <td><?php echo $product['stock_quantity']; ?></td>
                <td><?php echo $product['reorder_level']; ?></td>
                <td><?php echo number_format($product['stock_value'], 2); ?></td>
                <td>
                    <?php if ($product['stock_quantity'] <= $product['reorder_level']): ?>
                        <span class="badge bg-danger">Stock Bajo</span>
                    <?php else: ?>
                        <span class="badge bg-success">OK</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    
    <div class="no-print">
        <h3>Movimientos Recientes de Inventario</h3>
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo de Movimiento</th>
                <th>Cantidad</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData['movements'] as $movement): ?>
            <tr>
                <td><?php echo $movement['created_at']; ?></td>
                <td><?php echo $movement['product_name']; ?></td>
                <td><?php echo $movement['movement_type']; ?></td>
                <td><?php echo $movement['quantity']; ?></td>
                <td><?php echo $movement['user_name']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-container, #printable-container * {
            visibility: visible;
        }
        #printable-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
        }
        .no-print {
            display: none;
        }
    }
</style>