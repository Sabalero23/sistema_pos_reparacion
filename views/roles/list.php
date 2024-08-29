<div class="container mt-4">
    <h1 class="mb-4">Gestión de Roles</h1>
    
    <a href="<?php echo url('roles.php?action=add'); ?>" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Añadir Rol
    </a>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="rolesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?php echo $role['id']; ?></td>
                        <td><?php echo htmlspecialchars($role['name']); ?></td>
                        <td>
                            <a href="<?php echo url('roles.php?action=edit&id=' . $role['id']); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="<?php echo url('roles.php?action=permissions&id=' . $role['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-key"></i> Permisos
                            </a>
                            <button class="btn btn-sm btn-danger delete-role" data-id="<?php echo $role['id']; ?>">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo url('js/roles.js'); ?>"></script>