<div class="container mt-4">
    <h1 class="mb-4">Editar Términos y Condiciones</h1>
    
    <form action="<?php echo url('manage_terms.php?action=edit&id=' . $term['id']); ?>" method="post">
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($term['content']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Términos</button>
        <a href="<?php echo url('manage_terms.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>