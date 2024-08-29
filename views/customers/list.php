<div class="container mt-4">
    <h1 class="mb-4">Gestión de Clientes</h1>
    
    <?php if (hasPermission('customers_create')): ?>
        <a href="<?php echo url('customers.php?action=add'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Añadir Cliente
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="customersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo $customer['id']; ?></td>
                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                        <td>
                            <?php if (hasPermission('customers_edit')): ?>
                                <a href="<?php echo url('customers.php?action=edit&id=' . $customer['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('customers_delete')): ?>
                                <button class="btn btn-sm btn-danger delete-customer" data-id="<?php echo $customer['id']; ?>">
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

<script src="<?php echo url('js/customers.js'); ?>"></script>