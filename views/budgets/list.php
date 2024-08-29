<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1>Listado de Presupuestos</h1>
    
    <?php if (hasPermission('budget_create')): ?>
        <a href="<?php echo url('budget.php?action=create'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Nuevo Presupuesto
        </a>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($budgets as $budget): ?>
                <tr>
                    <td><?php echo $budget['budget_date']; ?></td>
                    <td><?php echo htmlspecialchars($budget['customer_name'] ?? 'N/A'); ?></td>
                    <td><?php echo number_format($budget['total_amount'], 2); ?></td>
                    <td><?php echo ucfirst($budget['status']); ?></td>
                    <td>
                        <a href="<?php echo url('budget.php?action=view&id=' . $budget['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                        <?php if (hasPermission('budget_edit')): ?>
                            <a href="<?php echo url('budget.php?action=edit&id=' . $budget['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                        <?php endif; ?>
                        <?php if (hasPermission('budget_delete')): ?>
                            <a href="<?php echo url('budget.php?action=delete&id=' . $budget['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de querer eliminar este presupuesto?')">Eliminar</a>
                        <?php endif; ?>
                        <?php if (!empty($budget['customer_phone'])): ?>
    <button class="btn btn-sm btn-success send-whatsapp" 
            data-phone="<?php echo htmlspecialchars($budget['customer_phone']); ?>"
            data-name="<?php echo htmlspecialchars($budget['customer_name']); ?>"
            data-id="<?php echo htmlspecialchars($budget['id']); ?>"
            data-token="<?php echo !empty($budget['view_token']) ? htmlspecialchars($budget['view_token']) : ''; ?>">
        Enviar WhatsApp
    </button>
<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const whatsappButtons = document.querySelectorAll('.send-whatsapp');
    
    whatsappButtons.forEach(button => {
        button.addEventListener('click', function() {
            const phone = this.getAttribute('data-phone');
            const name = this.getAttribute('data-name');
            const budgetId = this.getAttribute('data-id');
            const token = this.getAttribute('data-token');
            
            // Verificar que budgetId y token no sean null o undefined
            if (!budgetId || !token) {
                console.error('Error: budgetId o token no definidos');
                alert('Error al generar el enlace del presupuesto');
                return;
            }

            // Usar la función url() si está disponible, de lo contrario construir la URL manualmente
            const baseUrl = typeof url === 'function' ? url('') : 'https://taller.whaticket.com.ar/';
            const clientUrl = `${baseUrl}views/budgets/view_cliente.php?id=${budgetId}&token=${token}`;
            
            console.log('URL generada:', clientUrl); // Para depuración

            const message = encodeURIComponent(`Hola ${name}, su presupuesto se ha creado con éxito. Puede verlo e imprimirlo desde aquí: ${clientUrl}`);
            
            const whatsappUrl = `https://api.whatsapp.com/send?phone=${phone}&text=${message}`;
            
            window.open(whatsappUrl, '_blank');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>