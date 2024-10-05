<?php
$permissionsByCategory = [
    'Usuarios' => ['users_view', 'users_create', 'users_edit', 'users_delete'],
    'Productos' => ['products_view', 'products_create', 'products_edit', 'products_delete'],
    'Categorías' => ['categories_view', 'categories_create', 'categories_edit', 'categories_delete'],
    'Ventas' => ['sales_view', 'sales_create', 'sales_edit', 'sales_cancel'],
    'Presupuestos' => ['budget_view', 'budget_create', 'budget_edit', 'budget_delete', 'budget_change_status'],
    'Caja registradora' => ['cash_register_manage', 'cash_register_open', 'cash_register_close', 'cash_register_movement', 'cash_register_edit'],
    'Proveedores' => ['suppliers_view', 'suppliers_create', 'suppliers_edit', 'suppliers_delete'],
    'Clientes' => ['customers_view', 'customers_create', 'customers_edit', 'customers_delete'],
    'Cuentas de clientes' => ['customer_accounts_view', 'customer_accounts_adjust', 'customer_accounts_add'],
    'Pagos' => ['payments_view', 'payments_create', 'payments_edit', 'payments_delete'],
    'Órdenes de servicio' => ['services_view', 'services_create', 'services_edit', 'services_delete', 'services_update_status'],
    'Visitas a Domicilio' => ['home_visits_view', 'home_visits_create', 'home_visits_edit', 'home_visits_delete', 'calendar_view', 'home_visits_send_whatsapp'],
    'Servicios Remotos' => ['remote_services_view', 'remote_services_create', 'remote_services_edit', 'remote_services_delete', 'remote_services_update_status', 'remote_services_send_notification'],
    'Reportes' => ['reports_view', 'reports_generate'],
    'Roles y Permisos' => ['roles_manage'],
    'Reservas' => ['reservations_view', 'reservations_create', 'reservations_edit', 'reservations_delete', 'reservations_confirm', 'reservations_cancel', 'reservations_convert'],
    'Promociones' => ['promotions_view', 'promotions_create', 'promotions_edit', 'promotions_delete', 'promotions_apply'],
    'Inventario' => ['inventory_view', 'inventory_update', 'inventory_adjust', 'inventory_view_movements', 'inventory_view_low_stock'],
    'Compras' => ['purchases_view', 'purchases_create', 'purchases_edit', 'purchases_delete', 'purchases_receive', 'purchases_view_movements'],
    'Configuración del sistema' => ['settings_view', 'settings_edit', 'company_settings_view'],
    'Auditoría' => ['audit_view'],
    'Backup y Restauración' => ['backup_create', 'backup_restore', 'backup_delete', 'backup_download']
];

$allPermissionsKeyValue = array_column($allPermissions, 'description', 'key');
$rolePermissionsKeys = array_column($rolePermissions, 'key');

function sanitizeCategoryName($category) {
    return strtolower(str_replace([' ', 'ó'], ['_', 'o'], $category));
}

$isAdminRole = $role['name'] === 'Administrador'; // Asumimos que el rol de administrador se llama "Administrador"
?>

<div class="container mt-4">
    <h1 class="mb-4">Permisos del Rol: <?php echo htmlspecialchars($role['name']); ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="<?php echo url('roles.php?action=permissions&id=' . $role['id']); ?>" method="post">
        <?php if ($isAdminRole): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                    <label class="form-check-label" for="selectAllPermissions">
                        <strong>Seleccionar todos los permisos</strong>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <div class="accordion" id="permissionsAccordion">
            <?php foreach ($permissionsByCategory as $category => $permissions): ?>
                <?php $categoryId = sanitizeCategoryName($category); ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $categoryId; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $categoryId; ?>" aria-expanded="false" aria-controls="collapse<?php echo $categoryId; ?>">
                            <?php echo $category; ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $categoryId; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $categoryId; ?>" data-bs-parent="#permissionsAccordion">
                        <div class="accordion-body">
                            <?php foreach ($permissions as $permission): ?>
                                <?php if (isset($allPermissionsKeyValue[$permission])): ?>
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                            value="<?php echo $permission; ?>" 
                                            id="permission_<?php echo $permission; ?>"
                                            <?php echo in_array($permission, $rolePermissionsKeys) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="permission_<?php echo $permission; ?>">
                                            <?php echo htmlspecialchars($allPermissionsKeyValue[$permission]); ?>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Actualizar Permisos</button>
        <a href="<?php echo url('roles.php'); ?>" class="btn btn-secondary mt-4">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectAllCheckbox = document.getElementById('selectAllPermissions');
    var permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            permissionCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }
});
</script>