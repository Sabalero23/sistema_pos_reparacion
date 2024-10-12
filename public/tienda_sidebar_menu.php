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

<nav class="sidebar">
    <div class="logo">
        <?php if (!empty($companyInfo['logo_path'])): ?>
            <img src="<?php echo corregirURLLogo($companyInfo['logo_path']); ?>" alt="<?php echo htmlspecialchars($companyInfo['name']); ?>" class="company-logo">
        <?php else: ?>
            <a href="<?php echo url('/'); ?>"><?php echo htmlspecialchars($companyInfo['name'] ?? 'Nuestra Tienda'); ?></a>
        <?php endif; ?>
    </div>
    <ul>
        <li><a href="<?php echo url('/tienda.php'); ?>" class="<?php echo !$categoria_id ? 'active' : ''; ?>" data-categoria-id="">Todos los productos</a></li>
        <?php foreach ($categorias as $categoria): ?>
            <li><a href="<?php echo url('/tienda.php?categoria=' . $categoria['id']); ?>" class="<?php echo $categoria_id == $categoria['id'] ? 'active' : ''; ?>" data-categoria-id="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>