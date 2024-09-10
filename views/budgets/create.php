<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1>Crear Nuevo Presupuesto</h1>
    
    <form action="<?php echo url('budget.php?action=create'); ?>" method="post" id="budgetForm">
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" id="customer_search" class="form-control" placeholder="Buscar cliente" required>
            <input type="hidden" name="customer_id" id="customer_id" required>
        </div>

        <div id="productList">
            <div class="product-item mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control product-search" placeholder="Buscar producto" required>
                        <input type="hidden" name="items[0][product_id]" class="product-id" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][quantity]" class="form-control quantity" placeholder="Cantidad" required min="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][price]" class="form-control price" placeholder="Precio" required step="0.01">
                    </div>
                    <div class="col-md-2">
                        <span class="form-control-plaintext subtotal">0.00</span>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="addProduct" class="btn btn-secondary mb-3">Añadir Producto</button>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total</label>
            <input type="number" name="total_amount" id="total_amount" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label for="validity_period" class="form-label">Período de Validez (días)</label>
            <input type="number" name="validity_period" id="validity_period" class="form-control" value="30" required min="1">
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Crear Presupuesto</button>
    </form>
</div>

<!-- Asegúrate de que estas líneas estén en el orden correcto y antes de tu script personalizado -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
// Usamos una función de carga diferida para asegurarnos de que jQuery y jQuery UI estén disponibles
function loadScript(url, callback) {
    var script = document.createElement("script");
    script.type = "text/javascript";
    if (script.readyState) {  // IE
        script.onreadystatechange = function() {
            if (script.readyState === "loaded" || script.readyState === "complete") {
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  // Otros navegadores
        script.onload = function() {
            callback();
        };
    }
    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}

// Cargamos jQuery UI después de que jQuery se haya cargado
loadScript("https://code.jquery.com/jquery-3.6.0.min.js", function() {
    loadScript("https://code.jquery.com/ui/1.12.1/jquery-ui.min.js", function() {
        // Aquí va todo el código que utiliza jQuery y jQuery UI
        $(document).ready(function() {
            const productList = document.getElementById('productList');
            const addProductBtn = document.getElementById('addProduct');
            let productCount = 1;

            // Autocompletado para clientes
            $("#customer_search").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "<?php echo url('api/search.php'); ?>",
                        dataType: "json",
                        data: {
                            term: request.term,
                            type: 'customer'
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.name,
                                    value: item.name,
                                    id: item.id
                                };
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $("#customer_id").val(ui.item.id);
                }
            });

            // Función para inicializar el autocompletado de productos
            function initProductAutocomplete(element) {
                $(element).autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "<?php echo url('api/search.php'); ?>",
                            dataType: "json",
                            data: {
                                term: request.term,
                                type: 'product'
                            },
                            success: function(data) {
                                response($.map(data, function(item) {
                                    return {
                                        label: item.name,
                                        value: item.name,
                                        id: item.id,
                                        price: item.price
                                    };
                                }));
                            }
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        $(this).closest('.product-item').find('.product-id').val(ui.item.id);
                        $(this).closest('.product-item').find('.price').val(ui.item.price);
                        updateSubtotal($(this).closest('.product-item'));
                    }
                });
            }

            function updateSubtotal(row) {
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.price').val()) || 0;
                const subtotal = quantity * price;
                row.find('.subtotal').text(subtotal.toFixed(2));
                updateTotal();
            }

            function updateTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#total_amount').val(total.toFixed(2));
            }

            function initializeRow(row) {
                const quantityInput = row.querySelector('.quantity');
                const priceInput = row.querySelector('.price');

                initProductAutocomplete(row.querySelector('.product-search'));

                quantityInput.addEventListener('input', function() {
                    updateSubtotal($(this).closest('.product-item'));
                });

                priceInput.addEventListener('input', function() {
                    updateSubtotal($(this).closest('.product-item'));
                });

                row.querySelector('.remove-product').addEventListener('click', function() {
                    if (productList.children.length > 1) {
                        row.remove();
                        updateTotal();
                    } else {
                        alert('Debe haber al menos un producto en el presupuesto.');
                    }
                });
            }

            addProductBtn.addEventListener('click', function() {
                const newRow = productList.children[0].cloneNode(true);
                newRow.querySelectorAll('input').forEach(function(input) {
                    input.value = '';
                    if (input.name) {
                        input.name = input.name.replace('[0]', `[${productCount}]`);
                    }
                });
                newRow.querySelector('.subtotal').textContent = '0.00';
                productList.appendChild(newRow);
                initializeRow(newRow);
                productCount++;
            });

            productList.querySelectorAll('.product-item').forEach(initializeRow);
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
