<div class="container mt-4">
    <h1 class="mb-4">Gestión de Términos y Condiciones</h1>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Contenido</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($terms as $term): ?>
            <tr>
                <td><?php echo htmlspecialchars($term['id']); ?></td>
                <td><?php echo substr(htmlspecialchars($term['content']), 0, 100) . '...'; ?></td>
                <td><?php echo $term['active'] ? 'Activo' : 'Inactivo'; ?></td>
                <td><?php echo htmlspecialchars($term['created_at']); ?></td>
                <td>
                    <a href="<?php echo url('manage_terms.php?action=edit&id=' . $term['id']); ?>" class="btn btn-sm btn-info">Editar</a>
                    <?php if (!$term['active']): ?>
                    <a href="<?php echo url('manage_terms.php?action=activate&id=' . $term['id']); ?>" class="btn btn-sm btn-success">Activar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>