<?php
ob_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/roles.php';
require_once __DIR__ . '/home_visit_functions.php';

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle logout
if (isset($_GET['logout'])) {
    logout();
}

// Get system settings
$settings = getSettings();
$logoPath = $settings['logo_path'] ?? '/uploads/logo.png';
$appName = $settings['app_name'] ?? APP_NAME;

// Obtener visitas programadas
$scheduledVisits = getScheduledHomeVisits();
$scheduledVisitsCount = count($scheduledVisits);

// Helper function to check if a menu item should be active
function isActiveMenuItem($pageName) {
    return strpos($_SERVER['PHP_SELF'], $pageName) !== false ? 'active' : '';
}

// Helper function to generate menu items
function generateMenuItem($url, $icon, $label, $permission, $pageName = null) {
    if (hasPermission($permission)) {
        $active = $pageName ? isActiveMenuItem($pageName) : '';
        return "<a href='" . url($url) . "' class='list-group-item list-group-item-action $active'>
                    <i class='$icon me-2'></i>$label
                </a>";
    }
    return '';
}

// Helper function to generate submenu
function generateSubmenu($title, $icon, $items, $id) {
    $submenu = '';
    $hasPermission = false;
    foreach ($items as $item) {
        if (hasPermission($item['permission'])) {
            $hasPermission = true;
            break;
        }
    }

    if ($hasPermission) {
        $submenu = "<div class='list-group-item list-group-item-action'>
                        <a href='#{$id}Submenu' data-bs-toggle='collapse' aria-expanded='false' class='dropdown-toggle'>
                            <i class='$icon me-2'></i>$title
                        </a>
                        <ul class='collapse list-unstyled' id='{$id}Submenu'>";

        foreach ($items as $item) {
            if (hasPermission($item['permission'])) {
                $active = isActiveMenuItem($item['url']);
                $submenu .= "<li>
                                <a href='" . url($item['url']) . "' class='list-group-item list-group-item-action $active'>
                                    <i class='fas fa-caret-right me-2'></i>{$item['label']}
                                </a>
                             </li>";
            }
        }

        $submenu .= "</ul></div>";
    }

    return $submenu;
}

// Define submenu items
$salesSubmenuItems = [
    ['url' => 'sales.php', 'label' => 'Ventas', 'permission' => 'sales_view'],
    ['url' => 'budget.php', 'label' => 'Presupuestos', 'permission' => 'budget_view'],
    ['url' => 'purchases.php', 'label' => 'Compras', 'permission' => 'purchases_view'],
    ['url' => 'reservations.php', 'label' => 'Reservas', 'permission' => 'reservations_view'],
    ['url' => 'promotions.php', 'label' => 'Promociones', 'permission' => 'promotions_view'],
];

$inventorySubmenuItems = [
    ['url' => 'products.php', 'label' => 'Productos', 'permission' => 'products_view'],
    ['url' => 'categories.php', 'label' => 'Categorías', 'permission' => 'categories_view'],
    ['url' => 'suppliers.php', 'label' => 'Proveedores', 'permission' => 'suppliers_view'],
    ['url' => 'inventory.php', 'label' => 'Inventario', 'permission' => 'inventory_view'],
];

$cashRegisterSubmenuItems = [
    ['url' => 'cash_register.php', 'label' => 'Estado de caja', 'permission' => 'cash_register_manage'],
    ['url' => 'cash_register.php?action=open', 'label' => 'Abrir caja', 'permission' => 'cash_register_manage'],
    ['url' => 'cash_register.php?action=close', 'label' => 'Cerrar caja', 'permission' => 'cash_register_manage'],
    ['url' => 'cash_register.php?action=movement', 'label' => 'Registrar movimiento', 'permission' => 'cash_register_manage'],
];

$customerAccountsSubmenuItems = [
    ['url' => 'customers.php', 'label' => 'Clientes', 'permission' => 'customers_view'],
    ['url' => 'customer_accounts.php', 'label' => 'Cuentas de Clientes', 'permission' => 'customer_accounts_view'],
];

$repairSubmenuItems = [
    ['url' => 'services.php', 'label' => 'Órdenes de Servicio', 'permission' => 'services_view'],
    ['url' => 'home_visits.php', 'label' => 'Visitas a Domicilio', 'permission' => 'home_visits_view'],
    ['url' => 'calendar.php', 'label' => 'Calendario', 'permission' => 'calendar_view'],
];

$usersSubmenuItems = [
    ['url' => 'users.php', 'label' => 'Usuarios', 'permission' => 'users_view'],
    ['url' => 'roles.php', 'label' => 'Roles', 'permission' => 'roles_manage'],
];

$configSubmenuItems = [
    ['url' => 'settings.php', 'label' => 'Configuración General', 'permission' => 'settings_view'],
    ['url' => 'company_settings.php', 'label' => 'Datos de la Empresa', 'permission' => 'company_settings_view'],
    ['url' => 'manage_terms.php', 'label' => 'Términos y Condiciones', 'permission' => 'settings_view'],
    ['url' => 'backup.php', 'label' => 'Copias de Seguridad', 'permission' => 'backup_create'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo url('/assets/css/all.min.css'); ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Roboto+Mono&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo url('/assets/css/styles.css'); ?>">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading d-flex align-items-center">
                <a href="<?php echo url('index.php'); ?>" class="d-flex align-items-center text-decoration-none">
                    <img src="<?php echo url($logoPath); ?>" alt="Logo del Sistema" class="system-logo me-2">
                    <span class="fs-5 fw-semibold text-white"><?php echo htmlspecialchars($appName); ?></span>
                </a>
            </div>
            <div class="list-group list-group-flush">
                <?php if (isLoggedIn()): ?>
                    <?php
                    echo generateMenuItem('index.php', 'fas fa-home', 'Inicio', 'home_view', 'index.php');
                    echo generateSubmenu('Ventas y Compras', 'fas fa-shopping-cart', $salesSubmenuItems, 'sales');
                    echo generateSubmenu('Inventario', 'fas fa-box', $inventorySubmenuItems, 'inventory');
                    echo generateSubmenu('Caja', 'fas fa-cash-register', $cashRegisterSubmenuItems, 'cashRegister');
                    echo generateSubmenu('Clientes', 'fas fa-user-circle', $customerAccountsSubmenuItems, 'customerAccounts');
                    echo generateSubmenu('Reparaciones', 'fas fa-tools', $repairSubmenuItems, 'repairs');
                    echo generateSubmenu('Usuarios', 'fas fa-users', $usersSubmenuItems, 'users');
                    echo generateSubmenu('Configuración', 'fas fa-cog', $configSubmenuItems, 'config');
                    echo generateMenuItem('reports.php', 'fas fa-chart-bar', 'Reportes', 'reports_view', 'reports.php');
                    ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button class="btn" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <div id="datetime-container" style="cursor: pointer;">
                        <span id="datetime"></span>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <?php if (isLoggedIn()): ?>
                                <?php if ($scheduledVisitsCount > 0): ?>
                                    <li class="nav-item dropdown me-3">
                                        <a class="nav-link" href="#" id="scheduledVisitsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-calendar-check"></i>
                                            <span class="badge bg-danger"><?php echo $scheduledVisitsCount; ?></span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="scheduledVisitsDropdown">
                                            <li><h6 class="dropdown-header">Visitas Programadas</h6></li>
                                            <?php foreach ($scheduledVisits as $visit): ?>
                                                <li><a class="dropdown-item" href="<?php echo url('home_visits.php?action=view&id=' . $visit['id']); ?>">
                                                    <?php echo htmlspecialchars($visit['customer_name'] . ' - ' . $visit['visit_date']); ?>
                                                </a></li>
                                            <?php endforeach; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="<?php echo url('home_visits.php'); ?>">Ver todas las visitas</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="<?php echo url('profile.php'); ?>"><i class="fas fa-id-card me-2"></i>Perfil</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo url('index.php?logout=true'); ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo url('login.php'); ?>">Iniciar sesión</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid mt-4">
                <?php
                if (isset($_SESSION['flash_message'])) {
                    $message = $_SESSION['flash_message'];
                    $type = $_SESSION['flash_type'] ?? 'info';
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                    echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                            {$message}
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
                }
                ?>
                <!-- Main content of the page starts here -->
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const wrapper = document.getElementById('wrapper');
        const toggleButton = document.getElementById('menu-toggle');
        const pageContent = document.getElementById('page-content-wrapper');
        const sidebar = document.getElementById('sidebar-wrapper');
        const userDropdown = document.getElementById('navbarDropdown');
        const scheduledVisitsDropdown = document.getElementById('scheduledVisitsDropdown');

        // Función para alternar el sidebar
        function toggleSidebar() {
            wrapper.classList.toggle('toggled');
            if (wrapper.classList.contains('toggled')) {
                sidebar.style.marginLeft = '0';
                if (window.innerWidth >= 768) {
                    pageContent.style.marginLeft = 'var(--sidebar-width)';
                }
            } else {
                sidebar.style.marginLeft = 'calc(-1 * var(--sidebar-width))';
                pageContent.style.marginLeft = '0';
            }
        }

        // Event listener para el botón de toggle del sidebar
        if (toggleButton) {
            toggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        // Manejo del dropdown del usuario
        if (userDropdown) {
            const userDropdownMenu = userDropdown.nextElementSibling;
            
            userDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdownMenu.classList.toggle('show');
            });

            // Cerrar el dropdown cuando se hace clic fuera de él
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        }

        // Manejo del dropdown de visitas programadas
        if (scheduledVisitsDropdown) {
            const scheduledVisitsMenu = scheduledVisitsDropdown.nextElementSibling;
            
            scheduledVisitsDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                scheduledVisitsMenu.classList.toggle('show');
            });

            // Cerrar el dropdown cuando se hace clic fuera de él
            document.addEventListener('click', function(e) {
                if (!scheduledVisitsDropdown.contains(e.target)) {
                    scheduledVisitsMenu.classList.remove('show');
                }
            });
        }

        // Manejar el redimensionamiento de la ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                wrapper.classList.remove('toggled');
                sidebar.style.marginLeft = 'calc(-1 * var(--sidebar-width))';
                pageContent.style.marginLeft = '0';
            } else if (wrapper.classList.contains('toggled')) {
                sidebar.style.marginLeft = '0';
                pageContent.style.marginLeft = 'var(--sidebar-width)';
            }
        });

        // Asegurar que el sidebar esté oculto en la carga inicial para dispositivos móviles
        if (window.innerWidth < 768) {
            wrapper.classList.remove('toggled');
            sidebar.style.marginLeft = 'calc(-1 * var(--sidebar-width))';
            pageContent.style.marginLeft = '0';
        }

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Confirmación para acciones de eliminación
        document.querySelectorAll('.delete-confirm').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3498db',
                    cancelButtonColor: '#e74c3c',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // Código para el datetime-container
        const datetimeContainer = document.getElementById('datetime-container');
        if (datetimeContainer) {
            datetimeContainer.addEventListener('click', function() {
                window.location.href = '/public/calendar.php'; // Asegúrate de que esta ruta sea correcta
            });
        }

        // Función para actualizar fecha y hora
        function updateDateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                timeZone: 'America/Argentina/Buenos_Aires' // Asegúrate de que esta sea la zona horaria correcta
            };
            const dateTimeString = now.toLocaleDateString('es-AR', options);
            const datetimeElement = document.getElementById('datetime');
            if (datetimeElement) {
                datetimeElement.textContent = dateTimeString;
            }
        }

        // Actualizar fecha y hora inicialmente y luego cada segundo
        updateDateTime();
        setInterval(updateDateTime, 1000);
    });
    </script>
</body>
</html>