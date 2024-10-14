<?php
require_once __DIR__ . '/../includes/tienda_functions.php';
require_once __DIR__ . '/../includes/utils.php';

$categorias = getCategorias();
$companyInfo = getCompanyInfo();
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;

// Función para corregir la URL del logo
function corregirURLLogo($url) {
    // Elimina '/public' duplicado si existe
    return preg_replace('#(/public)+#', '/public', $url);
}
?>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <?php if (!empty($companyInfo['logo_path'])): ?>
                <img src="<?php echo corregirURLLogo($companyInfo['logo_path']); ?>" alt="<?php echo htmlspecialchars($companyInfo['name']); ?>" class="company-logo">
            <?php else: ?>
                <a href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?></a>
            <?php endif; ?>
        </div>
        <button class="close-sidebar" id="closeSidebar">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="sidebar-content">
        <ul>
            <li>
                <a href="<?php echo url('/tienda.php'); ?>" 
                   class="<?php echo !$categoria_id ? 'active' : ''; ?>" 
                   data-categoria-id="">
                    Todos los productos
                </a>
            </li>
            <?php foreach ($categorias as $categoria): ?>
                <li>
                    <a href="<?php echo url('/tienda.php?categoria=' . $categoria['id']); ?>" 
                       class="<?php echo $categoria_id == $categoria['id'] ? 'active' : ''; ?>" 
                       data-categoria-id="<?php echo $categoria['id']; ?>">
                        <?php echo htmlspecialchars($categoria['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        height: 100vh;
        width: 250px;
        background-color: #f8f9fa;
        transition: left 0.3s ease-in-out;
        z-index: 1000;
        overflow-y: auto;
    }
    
    .sidebar.active {
        left: 0;
    }
    
    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #e9ecef;
    }
    
    .sidebar-content {
        padding: 1rem;
    }
    
    .close-sidebar {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
    
    @media (min-width: 768px) {
        .sidebar {
            left: 0;
        }
        
        .close-sidebar,
        .sidebar-overlay {
            display: none;
        }
    }
    
    .company-logo {
        max-width: 100%;
        height: auto;
    }
    
    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }
    
    .sidebar ul li {
        margin-bottom: 0.5rem;
    }
    
    .sidebar ul li a {
        display: block;
        padding: 0.5rem;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    
    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background-color: #e9ecef;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const closeSidebarButton = document.getElementById('closeSidebar');
    const toggleButton = document.querySelector('.toggle-sidebar');
    
    function openSidebar() {
        sidebar.classList.add('active');
        sidebarOverlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('active');
        sidebarOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    if (toggleButton) {
        toggleButton.addEventListener('click', openSidebar);
    }
    
    closeSidebarButton.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    
    // Cerrar el sidebar al seleccionar una categoría en móvil
    const categoryLinks = sidebar.querySelectorAll('a[data-categoria-id]');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                closeSidebar();
            }
        });
    });
    
    // Ajustar el sidebar al cambiar el tamaño de la ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('active');
            sidebarOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});
</script>