<div class="container mt-4">
    <h1 class="mb-4">Gestión de Compras</h1>
    
    <?php if (hasPermission('purchases_create')): ?>
        <a href="<?php echo url('purchases.php?action=create'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Nueva Compra
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="purchasesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?php echo $purchase['id']; ?></td>
                        <td><?php echo $purchase['purchase_date']; ?></td>
                        <td><?php echo htmlspecialchars($purchase['supplier_name'] ?? 'N/A'); ?></td>
                        <td><?php echo number_format($purchase['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst($purchase['status']); ?></td>
                        <td>
                            <a href="<?php echo url('purchases.php?action=view&id=' . $purchase['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <?php if ($purchase['status'] === 'pendiente' && hasPermission('purchases_receive')): ?>
                                <a href="<?php echo url('purchases.php?action=receive&id=' . $purchase['id']); ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Recibir
                                </a>
                            <?php endif; ?>
                            <?php if ($purchase['status'] === 'pendiente' && hasPermission('purchases_cancel')): ?>
                                <button class="btn btn-sm btn-danger cancel-purchase" data-id="<?php echo $purchase['id']; ?>">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            <?php endif; ?>
                            <?php if (hasPermission('purchases_view_movements')): ?>
                                <a href="<?php echo url('purchases.php?action=movements&id=' . $purchase['id']); ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-history"></i> Movimientos
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo url('js/purchases.js'); ?>"></script>