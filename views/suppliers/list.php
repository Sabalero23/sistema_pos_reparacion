<div class="container mt-4">
    <h1 class="mb-4">Gestión de Proveedores</h1>
    
    <?php if (hasPermission('suppliers_create')): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?php echo url('suppliers.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Proveedor
            </a>
            <a href="<?php echo url('import_suppliers.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-file-import"></i> Importar Proveedores
            </a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="suppliersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?php echo $supplier['id']; ?></td>
                        <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['phone']); ?></td>
                        <td>
                            <?php if (hasPermission('suppliers_edit')): ?>
                                <a href="<?php echo url('suppliers.php?action=edit&id=' . $supplier['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('suppliers_delete')): ?>
                                <button class="btn btn-sm btn-danger delete-supplier" data-id="<?php echo $supplier['id']; ?>">
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

<script src="<?php echo url('js/suppliers.js'); ?>"></script>