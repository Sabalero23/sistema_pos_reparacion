<?php
// Verificar si el usuario está autenticado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Lista de Visitas a Domicilio";

// Función para generar la URL de WhatsApp
function generateWhatsAppUrl($phoneNumber, $message, $visitId, $accessToken) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $viewUrl = $baseUrl . "/public/view_cliente.php?token=" . urlencode($accessToken);
    
    // Creamos un mensaje corto para el enlace de WhatsApp
    $shortMessage = "Detalles de su visita a domicilio: " . $viewUrl;
    
    // Codificamos el mensaje corto para la URL
    $encodedMessage = urlencode($shortMessage);
    
    // Generamos la URL de WhatsApp con el mensaje corto
    return "https://wa.me/" . preg_replace('/[^0-9]/', '', $phoneNumber) . "?text=" . $encodedMessage;
}

// Obtener todas las visitas
$visits = getAllHomeVisits();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Lista de Visitas a Domicilio</h1>
    <?php if (hasPermission('home_visits_create')): ?>
        <a href="<?php echo url('home_visits.php?action=create'); ?>" class="btn btn-primary mb-3">Nueva Visita</a>
    <?php endif; ?>
    
    <table id="homeVisitsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
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
                <td><?php echo htmlspecialchars($visit['id']); ?></td>
                <td><?php echo htmlspecialchars($visit['customer_name']); ?></td>
                <td data-order="<?php echo strtotime($visit['visit_date']); ?>">
                    <?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?>
                </td>
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
                    <?php if (hasPermission('home_visits_edit')): ?>
                        <a href="<?php echo url('home_visits.php?action=edit&id=' . $visit['id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                    <?php endif; ?>
                    <?php if (hasPermission('home_visits_delete')): ?>
                        <button class="btn btn-sm btn-danger delete-visit" data-id="<?php echo $visit['id']; ?>">Eliminar</button>
                    <?php endif; ?>
                    <?php if (!empty($visit['customer_phone']) && !empty($visit['access_token'])): 
                        $message = "Hola {$visit['customer_name']}, su visita a domicilio ha sido programada para el " . date('d/m/Y', strtotime($visit['visit_date'])) . " a las " . date('H:i', strtotime($visit['visit_time'])) . ".";
                        $whatsappUrl = generateWhatsAppUrl($visit['customer_phone'], $message, $visit['id'], $visit['access_token']);
                    ?>
                    <a href="<?php echo $whatsappUrl; ?>" class="btn btn-sm btn-success send-whatsapp" 
                       data-fullmessage="<?php echo htmlspecialchars($message); ?>" target="_blank">
                        Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#homeVisitsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[2, "desc"], [3, "desc"]], // Ordenar por fecha y hora de forma descendente
        "columnDefs": [
            { "orderable": false, "targets": 5 } // Hace que la columna de acciones no sea ordenable
        ]
    });

    $('.delete-visit').on('click', function() {
        var visitId = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("home_visits.php?action=delete"); ?>',
                    method: 'POST',
                    data: { id: visitId },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Eliminado!',
                                'La visita ha sido eliminada.',
                                'success'
                            );
                            table.row($(this).parents('tr')).remove().draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                'No se pudo eliminar la visita.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Ocurrió un error al intentar eliminar la visita.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    $('.send-whatsapp').on('click', function(e) {
        e.preventDefault();
        var whatsappUrl = $(this).attr('href');
        var fullMessage = $(this).data('fullmessage');
        
        // Abre WhatsApp Web o la aplicación móvil con el enlace corto
        window.open(whatsappUrl, '_blank');
        
        // Muestra un modal con el mensaje completo para copiar
        Swal.fire({
            title: 'Mensaje para WhatsApp',
            html: '<textarea class="form-control" rows="5" id="fullMessage" readonly>' + fullMessage + '</textarea>',
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'Copiar mensaje',
            cancelButtonText: 'Cerrar',
            preConfirm: () => {
                var messageElem = document.getElementById('fullMessage');
                messageElem.select();
                document.execCommand('copy');
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Copiado', 'El mensaje ha sido copiado al portapapeles', 'success');
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>