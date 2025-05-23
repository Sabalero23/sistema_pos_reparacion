<?php
// Asegúrate de que tienes acceso a las funciones necesarias y la configuración
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';

// Verifica los permisos del usuario
if (!isLoggedIn() || !hasPermission('home_visits_create')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programar Nueva Visita a Domicilio</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    

</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Programar Nueva Visita a Domicilio</h1>

        <form action="<?php echo url('home_visits.php?action=create'); ?>" method="post" id="homeVisitForm">
            <div class="mb-3">
                <label for="customer_search" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente" required autocomplete="off">
                <input type="hidden" id="customer_id" name="customer_id" required>
            </div>

            <div class="mb-3">
                <label for="visit_date" class="form-label">Fecha de Visita</label>
                <input type="date" class="form-control" id="visit_date" name="visit_date" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="visit_time" class="form-label">Hora de Visita</label>
                <input type="time" class="form-control" id="visit_time" name="visit_time" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notas</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" autocomplete="off"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Programar Visita</button>
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
                            <input type="text" class="form-control" id="clientName" name="name" required autocomplete="name">
                        </div>
                        <div class="mb-3">
                            <label for="clientEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="clientEmail" name="email" autocomplete="email">
                        </div>
                        <div class="mb-3">
                            <label for="clientPhone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="clientPhone" name="phone" pattern="[0-9]{10}" autocomplete="tel">
                        </div>
                        <div class="mb-3">
                            <label for="clientAddress" class="form-label">Dirección</label>
                            <textarea class="form-control" id="clientAddress" name="address" rows="3" autocomplete="street-address"></textarea>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var baseUrl = '<?php echo url(''); ?>';
    </script>
    
    <!-- Custom script -->
    <script src="<?php echo url('js/home_visits.js'); ?>"></script>
</body>
</html>