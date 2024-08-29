<div class="container mt-4">
    <h1 class="mb-4">Movimientos de Compra #<?php echo $purchase['id']; ?></h1>

    <a href="<?php echo url('purchases.php?action=view&id=' . $purchase['id']); ?>" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver a la compra
    </a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Movimiento</th>
                    <th>Usuario</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movements as $movement): ?>
                    <tr>
                        <td><?php echo $movement['created_at']; ?></td>
                        <td><?php echo ucfirst($movement['movement_type']); ?></td>
                        <td><?php echo htmlspecialchars($movement['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($movement['details']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>