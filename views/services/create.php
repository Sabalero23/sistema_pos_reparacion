<?php
$pageTitle = $pageTitle ?? "Nueva Orden de Trabajo";
$error = $error ?? null;
$terms = $terms ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000 !important;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
            + Cliente
        </button>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form action="<?php echo htmlspecialchars(url('services.php?action=create')); ?>" method="post" id="serviceForm">
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente" required>
            <input type="hidden" id="customer_id" name="customer_id" required>
        </div>
        
        <div class="mb-3">
            <label for="brand" class="form-label">Marca</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
        </div>
        
        <div class="mb-3">
            <label for="model" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="model" name="model" required>
        </div>
        
        <div class="mb-3">
            <label for="serial_number" class="form-label">Número de Serie</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number">
        </div>
        
        <div id="serviceItems">
            <div class="service-item mb-3">
                <label class="form-label">Servicio</label>
                <input type="text" class="form-control mb-2" name="services[0][description]" placeholder="Descripción del servicio" required>
                <input type="number" class="form-control service-cost" name="services[0][cost]" placeholder="Costo" step="0.01" required>
            </div>
        </div>
        
        <button type="button" class="btn btn-secondary mb-3" id="addService">Agregar Servicio</button>
        
        <div class="mb-3">
            <label for="total_amount" class="form-label">Total</label>
            <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" required readonly>
        </div>
        
        <div class="mb-3">
            <label for="prepaid_amount" class="form-label">Monto Prepago</label>
            <input type="number" class="form-control" id="prepaid_amount" name="prepaid_amount" step="0.01" required>
        </div>
        
        <div class="mb-3">
            <label for="warranty" class="form-label">Garantía</label>
            <select class="form-control" id="warranty" name="warranty">
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>
        </div>
        
        <div class="mb-3">
            <h5>Términos y Condiciones</h5>
            <div class="border p-3 mb-2">
                <?php echo nl2br(htmlspecialchars($terms)); ?>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                <label class="form-check-label" for="acceptTerms">
                    Acepto los términos y condiciones
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Orden de Servicio</button>
    </form>
</div>

<!-- Modal para crear nuevo cliente -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newClientModalLabel">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newClientForm">
                    <div class="mb-3">
                        <label for="clientName" class="form-label">Nombre del Cliente</label>
                        <input type="text" class="form-control" id="clientName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="clientEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="clientEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="clientPhone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="clientPhone" name="phone" pattern="[0-9]{10}">
                    </div>
                    <div class="mb-3">
                        <label for="clientAddress" class="form-label">Dirección</label>
                        <textarea class="form-control" id="clientAddress" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveNewClient">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
jQuery.noConflict();
(function($) {
    $(document).ready(function() {
        let serviceCount = 1;
        const addServiceBtn = document.getElementById('addService');
        const serviceItems = document.getElementById('serviceItems');
        const totalAmount = document.getElementById('total_amount');
        const prepaidAmount = document.getElementById('prepaid_amount');
        const newClientModal = new bootstrap.Modal(document.getElementById('newClientModal'));
        const saveNewClientBtn = document.getElementById('saveNewClient');
        
        addServiceBtn.addEventListener('click', function() {
            const newService = document.createElement('div');
            newService.className = 'service-item mb-3';
            newService.innerHTML = `
                <label class="form-label">Servicio</label>
                <input type="text" class="form-control mb-2" name="services[${serviceCount}][description]" placeholder="Descripción del servicio" required>
                <input type="number" class="form-control service-cost" name="services[${serviceCount}][cost]" placeholder="Costo" step="0.01" required>
            `;
            serviceItems.appendChild(newService);
            serviceCount++;
            updateTotal();
        });

        serviceItems.addEventListener('input', function(e) {
            if (e.target.classList.contains('service-cost')) {
                updateTotal();
            }
        });

        prepaidAmount.addEventListener('input', updateTotal);

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.service-cost').forEach(function(input) {
                total += parseFloat(input.value) || 0;
            });
            totalAmount.value = total.toFixed(2);
            
            const prepaid = parseFloat(prepaidAmount.value) || 0;
            if (prepaid > total) {
                prepaidAmount.value = total.toFixed(2);
            }
        }

        document.getElementById('serviceForm').addEventListener('submit', function(e) {
            const total = parseFloat(totalAmount.value);
            const prepaid = parseFloat(prepaidAmount.value);
            
            if (prepaid > total) {
                e.preventDefault();
                alert('El monto prepago no puede ser mayor que el total.');
            }
        });

        function initializeAutocomplete() {
            $("#customer_search").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "<?php echo htmlspecialchars(url('services.php?action=searchCustomers')); ?>",
                        method: "GET",
                        dataType: "json",
                        data: { term: request.term },
                        success: function(data) {
                            if (data.error) {
                                console.error("Error en la búsqueda:", data.error);
                                response([]);
                            } else {
                                response(data);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en la solicitud AJAX:", status, error);
                            response([]);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $("#customer_id").val(ui.item.id);
                }
            });
        }

        initializeAutocomplete();

        saveNewClientBtn.addEventListener('click', function() {
    const form = document.getElementById('newClientForm');
    const formData = new FormData(form);

    fetch('<?php echo htmlspecialchars(url("services.php?action=addCustomer")); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar el modal
            bootstrap.Modal.getInstance(document.getElementById('newClientModal')).hide();
            
            // Mostrar mensaje de éxito con SweetAlert2 y luego recargar la página
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Cliente creado exitosamente.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Recargar la página
                    window.location.reload();
                }
            });
        } else {
            // Mostrar mensaje de error con SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error desconocido al crear el cliente.',
                footer: '<a href>Contacte al soporte técnico si el problema persiste.</a>'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de red al crear el cliente. Por favor, verifica tu conexión a internet y vuelve a intentarlo.'
        });
    });
});
    });
})(jQuery);
</script>