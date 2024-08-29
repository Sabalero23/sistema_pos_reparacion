document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    if ($.fn.DataTable.isDataTable('#productsTable')) {
        $('#productsTable').DataTable().destroy();
    }
    
    $('#productsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "order": [[1, "asc"]]  // Ordenar por nombre de producto (asumiendo que es la segunda columna)
    });

    // Manejar eliminación de productos
    const deleteButtons = document.querySelectorAll('.delete-product');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            
            if (confirm(`¿Estás seguro de que quieres eliminar el producto "${productName}"?`)) {
                fetch(`products.php?action=delete&id=${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ha ocurrido un error al intentar eliminar el producto.');
                });
            }
        });
    });

    // Validación del formulario de producto
    const productForm = document.querySelector('#productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            if (!validateProductForm()) {
                e.preventDefault();
            }
        });
    }

    // Función para validar el formulario de producto
    function validateProductForm() {
        let isValid = true;
        const name = document.querySelector('#name');
        const sku = document.querySelector('#sku');
        const price = document.querySelector('#price');
        const costPrice = document.querySelector('#cost_price');
        const stockQuantity = document.querySelector('#stock_quantity');
        const reorderLevel = document.querySelector('#reorder_level');

        if (!name.value.trim()) {
            isValid = false;
            showError(name, 'El nombre del producto es requerido.');
        } else {
            clearError(name);
        }

        if (!sku.value.trim()) {
            isValid = false;
            showError(sku, 'El SKU es requerido.');
        } else {
            clearError(sku);
        }

        if (!price.value || isNaN(price.value) || parseFloat(price.value) < 0) {
            isValid = false;
            showError(price, 'El precio debe ser un número positivo.');
        } else {
            clearError(price);
        }

        if (!costPrice.value || isNaN(costPrice.value) || parseFloat(costPrice.value) < 0) {
            isValid = false;
            showError(costPrice, 'El precio de costo debe ser un número positivo.');
        } else {
            clearError(costPrice);
        }

        if (!stockQuantity.value || isNaN(stockQuantity.value) || parseInt(stockQuantity.value) < 0) {
            isValid = false;
            showError(stockQuantity, 'La cantidad en stock debe ser un número entero positivo.');
        } else {
            clearError(stockQuantity);
        }

        if (!reorderLevel.value || isNaN(reorderLevel.value) || parseInt(reorderLevel.value) < 0) {
            isValid = false;
            showError(reorderLevel, 'El nivel de reorden debe ser un número entero positivo.');
        } else {
            clearError(reorderLevel);
        }

        return isValid;
    }

    // Función para mostrar errores en el formulario
    function showError(input, message) {
        const formControl = input.parentElement;
        const errorDiv = formControl.querySelector('.invalid-feedback') || document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerText = message;
        formControl.appendChild(errorDiv);
        input.classList.add('is-invalid');
    }

    // Función para limpiar errores en el formulario
    function clearError(input) {
        const formControl = input.parentElement;
        const errorDiv = formControl.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('is-invalid');
    }
});