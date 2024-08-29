<div class="container mt-4">
    <h1 class="mb-4">Reportes</h1>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Ventas</h5>
                    <p class="card-text">Genera un reporte detallado de las ventas en un período específico.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=sales'); ?>" method="post">
                        <div class="mb-3">
                            <label for="sales_start_date" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="sales_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="sales_end_date" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="sales_end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Compras</h5>
                    <p class="card-text">Genera un reporte detallado de las compras en un período específico.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=purchases'); ?>" method="post">
                        <div class="mb-3">
                            <label for="purchases_start_date" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="purchases_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="purchases_end_date" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="purchases_end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Inventario</h5>
                    <p class="card-text">Muestra el estado actual del inventario y los movimientos recientes.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=inventory'); ?>" method="post">
                        <div class="mb-3">
                            <label for="inventory_days" class="form-label">Movimientos de los últimos días</label>
                            <input type="number" class="form-control" id="inventory_days" name="days" value="30" min="1" max="365" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Clientes</h5>
                    <p class="card-text">Analiza las compras de los clientes en un período específico.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=customers'); ?>" method="post">
                        <div class="mb-3">
                            <label for="customers_start_date" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="customers_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="customers_end_date" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="customers_end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Movimientos de Caja</h5>
                    <p class="card-text">Muestra los movimientos diarios de caja, incluyendo aperturas, cierres y transacciones.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=cash_register'); ?>" method="post">
                        <div class="mb-3">
                            <label for="cash_register_start_date" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="cash_register_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="cash_register_end_date" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="cash_register_end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Cuentas por Cobrar</h5>
                    <p class="card-text">Muestra el estado actual de las cuentas por cobrar de los clientes.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=accounts_receivable'); ?>" method="post">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reporte de Servicios</h5>
                    <p class="card-text">Genera un reporte detallado de los servicios y órdenes de trabajo en un período específico.</p>
                    <form action="<?php echo url('reports.php?action=generate&type=services'); ?>" method="post">
                        <div class="mb-3">
                            <label for="services_start_date" class="form-label">Fecha de inicio</label>
                            <input type="date" class="form-control" id="services_start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="services_end_date" class="form-label">Fecha de fin</label>
                            <input type="date" class="form-control" id="services_end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>