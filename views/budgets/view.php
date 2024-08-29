<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1>Detalles del Presupuesto</h1>

    <div class="card mb-4">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <p><strong>ID de Presupuesto:</strong> <?php echo $budget['id']; ?></p>
            <p><strong>Fecha:</strong> <?php echo $budget['budget_date']; ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($budget['customer_name']); ?></p>
            <p><strong>Creado por:</strong> <?php echo htmlspecialchars($budget['user_name']); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($budget['status']); ?></p>
            <p><strong>Período de Validez:</strong> <?php echo $budget['validity_period']; ?> días</p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($budget['notes'])); ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Productos
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($budget['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th><?php echo number_format($budget['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mb-3">
        <a href="<?php echo url('/../views/budgets/budget_receipt.php?id=' . $budget['id']); ?>" class="btn btn-primary" target="_blank">
            <i class="fas fa-print"></i> Imprimir Presupuesto
        </a>
        <?php if (hasPermission('budget_edit') && $budget['status'] === 'pendiente'): ?>
            <a href="<?php echo url('budget.php?action=edit&id=' . $budget['id']); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Presupuesto
            </a>
        <?php endif; ?>
        <?php if ($budget['status'] === 'pendiente' && hasPermission('budget_approve')): ?>
            <a href="<?php echo url('budget.php?action=approve&id=' . $budget['id']); ?>" class="btn btn-success">
                <i class="fas fa-check"></i> Aprobar Presupuesto
            </a>
        <?php endif; ?>
        <?php if ($budget['status'] === 'pendiente' && hasPermission('budget_delete')): ?>
            <a href="<?php echo url('budget.php?action=delete&id=' . $budget['id']); ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de querer eliminar este presupuesto?')">
                <i class="fas fa-trash"></i> Eliminar Presupuesto
            </a>
        <?php endif; ?>
        <?php if ($budget['status'] === 'aprobado' && hasPermission('sales_create')): ?>
            <a href="<?php echo url('sales.php?action=create&from_budget=' . $budget['id']); ?>" class="btn btn-info">
                <i class="fas fa-cash-register"></i> Convertir a Venta
            </a>
        <?php endif; ?>
        <a href="<?php echo url('budget.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <?php if (hasPermission('budget_change_status')): ?>
        <form action="<?php echo url('budget.php?action=change_status'); ?>" method="post" class="mt-3">
            <input type="hidden" name="id" value="<?php echo $budget['id']; ?>">
            <div class="input-group">
                <select name="status" class="form-select">
                    <option value="pendiente" <?php if ($budget['status'] === 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="aprobado" <?php if ($budget['status'] === 'aprobado') echo 'selected'; ?>>Aprobado</option>
                    <option value="rechazado" <?php if ($budget['status'] === 'rechazado') echo 'selected'; ?>>Rechazado</option>
                    <option value="expirado" <?php if ($budget['status'] === 'expirado') echo 'selected'; ?>>Expirado</option>
                </select>
                <button type="submit" class="btn btn-primary">Cambiar Estado</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>