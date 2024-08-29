<div class="container mt-4">
    <h1 class="mb-4">Gestión de Categorías</h1>
    
    <?php if (hasPermission('categories_create')): ?>
            <a href="<?php echo url('categories.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Categoría
            </a>
        <?php endif; ?>
        <?php if (hasPermission('categories_create')): ?>
            <a href="<?php echo url('import_categories.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-file-import"></i> Importar Categorías
            </a>
        <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="categoriesTable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['description']); ?></td>
                        <td>
                            <?php if (hasPermission('categories_edit')): ?>
                                <a href="<?php echo url('categories.php?action=edit&id=' . $category['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('categories_delete')): ?>
                                <button class="btn btn-sm btn-danger delete-category" data-id="<?php echo $category['id']; ?>">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo url('js/categories.js'); ?>"></script>