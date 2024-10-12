<div class="container-fluid p-0">
    <h1 class="mb-4">Gestión de Productos</h1>
    
    <?php if (hasPermission('products_create')): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?php echo url('products.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Añadir Producto
            </a>
            <a href="<?php echo url('import_products.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-file-import"></i> Importar Productos
            </a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="productsTable">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Activo en Tienda</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?php echo $product['image_path']; ?>" alt="Imagen de <?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['sku']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock_quantity']; ?></td>
                        <td>
                            <?php if ($product['active_in_store']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (hasPermission('products_edit')): ?>
                                <a href="<?php echo url('products.php?action=edit&id=' . $product['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('products_delete')): ?>
                                <button class="btn btn-sm btn-danger delete-product" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>">
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

<script src="<?php echo url('js/products.js'); ?>"></script>

<style>
    .container-fluid.p-0 {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    @media (min-width: 768px) {
        .container-fluid.p-0 {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
    }
</style>