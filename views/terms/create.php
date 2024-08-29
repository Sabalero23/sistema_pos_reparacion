<div class="container mt-4">
    <h1 class="mb-4">Crear Nuevos Términos y Condiciones</h1>
    
    <form action="<?php echo url('manage_terms.php?action=create'); ?>" method="post">
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear Términos</button>
        <a href="<?php echo url('manage_terms.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>