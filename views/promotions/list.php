<div class="container mt-4">
    <h1 class="mb-4">Lista de Promociones</h1>
    
    <?php if (hasPermission('promotions_create')): ?>
        <a href="<?php echo url('promotions.php?action=create'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Nueva Promoción
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="promotionsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo de Descuento</th>
                    <th>Valor de Descuento</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Producto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promotion): ?>
                    <tr>
                        <td><?php echo $promotion['id']; ?></td>
                        <td><?php echo htmlspecialchars($promotion['name']); ?></td>
                        <td><?php echo htmlspecialchars($promotion['description']); ?></td>
                        <td><?php echo ucfirst($promotion['discount_type']); ?></td>
                        <td><?php echo $promotion['discount_value']; ?></td>
                        <td><?php echo $promotion['start_date']; ?></td>
                        <td><?php echo $promotion['end_date']; ?></td>
                        <td><?php echo htmlspecialchars($promotion['product_name'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if (hasPermission('promotions_edit')): ?>
                                <a href="<?php echo url('promotions.php?action=edit&id=' . $promotion['id']); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('promotions_delete')): ?>
                                <form action="<?php echo url('promotions.php?action=delete&id=' . $promotion['id']); ?>" method="post" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta promoción?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>