<div class="container mt-4">
    <h1 class="mb-4">Gestión de Usuarios</h1>
    
    <?php if (hasPermission('users_create')): ?>
        <a href="<?php echo url('users.php?action=add'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-user-plus"></i> Añadir Usuario
        </a>
    <?php endif; ?>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr data-user-id="<?php echo $user['id']; ?>">
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_name'] ?? 'Sin rol'); ?></td>
                        <td>
                            <?php if (hasPermission('users_edit')): ?>
                                <a href="<?php echo url('users.php?action=edit&id=' . $user['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('users_delete')): ?>
                                <button class="btn btn-sm btn-danger delete-user" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-md-none">
        <?php foreach ($users as $user): ?>
            <div class="card mb-3" data-user-id="<?php echo $user['id']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="card-text">
                        <strong>ID:</strong> <?php echo $user['id']; ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                        <strong>Rol:</strong> <?php echo htmlspecialchars($user['role_name'] ?? 'Sin rol'); ?>
                    </p>
                    <div class="btn-group" role="group">
                        <?php if (hasPermission('users_edit')): ?>
                            <a href="<?php echo url('users.php?action=edit&id=' . $user['id']); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        <?php endif; ?>
                        <?php if (hasPermission('users_delete')): ?>
                            <button class="btn btn-danger delete-user" data-id="<?php echo $user['id']; ?>">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="<?php echo url('js/users.js'); ?>"></script>

<style>
@media (max-width: 767px) {
    .btn-group {
        display: flex;
        margin-top: 10px;
    }
    .btn-group .btn {
        flex: 1;
    }
}
</style>