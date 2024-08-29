<div class="container mt-4">
    <h1 class="mb-4">Visitas a Domicilio</h1>

    <a href="<?php echo url('home_visits.php?action=create'); ?>" class="btn btn-primary mb-3">Programar Nueva Visita</a>

    <?php if (empty($visits)): ?>
        <p>No hay visitas programadas.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visits as $visit): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($visit['customer_name']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?></td>
                        <td><?php echo date('H:i', strtotime($visit['visit_time'])); ?></td>
                        <td>
    <?php
    $statusClass = '';
    switch($visit['status']) {
        case 'programada':
            $statusClass = 'badge bg-warning';
            break;
        case 'completada':
            $statusClass = 'badge bg-success';
            break;
        case 'cancelada':
            $statusClass = 'badge bg-danger';
            break;
        default:
            $statusClass = 'badge bg-secondary';
    }
    ?>
    <span class="<?php echo $statusClass; ?>"><?php echo ucfirst(htmlspecialchars($visit['status'])); ?></span>
</td>
                        <td>
                            <a href="<?php echo url('home_visits.php?action=view&id=' . $visit['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="<?php echo url('home_visits.php?action=edit&id=' . $visit['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="<?php echo url('home_visits.php?action=delete&id=' . $visit['id']); ?>" class="btn btn-sm btn-danger">Eliminar</a>
                            <?php if (!empty($visit['customer_phone'])): ?>
                                <button class="btn btn-sm btn-success send-whatsapp" 
                                        data-customer="<?php echo htmlspecialchars($visit['customer_name']); ?>"
                                        data-phone="<?php echo htmlspecialchars($visit['customer_phone']); ?>"
                                        data-date="<?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?>"
                                        data-time="<?php echo date('H:i', strtotime($visit['visit_time'])); ?>">
                                    Enviar WhatsApp
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('.table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    $('.send-whatsapp').on('click', function() {
        var customerName = $(this).data('customer');
        var phoneNumber = $(this).data('phone');
        var visitDate = $(this).data('date');
        var visitTime = $(this).data('time');

        var message = encodeURIComponent(`Hola ${customerName}, su Visita a Domicilio fue programada el ${visitDate} a las ${visitTime}. ¡Gracias!`);
        
        var whatsappUrl = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${message}`;
        
        window.open(whatsappUrl, '_blank');
    });
});
</script>