(function($) {
    $(function() {
        if (typeof $.ui === 'undefined' || typeof $.ui.autocomplete === 'undefined') {
            console.error('jQuery UI or autocomplete widget is not loaded');
            return;
        }

        const form = $('#purchaseForm');
        const productList = $('#productList');
        const addProductBtn = $('#addProduct');
        let productCount = 1;
        
        console.log('URL de la API:', baseUrl + '/api/search.php');


        $('#supplier_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: baseUrl + '/api/search.php',
                    dataType: "json",
                    data: {
                        term: request.term,
                        type: 'supplier'
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                id: item.id
                            };
                        }));
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la búsqueda de proveedores:", status, error);
                        response([]);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                $('#supplier_id').val(ui.item.id);
            }
        });

                function initProductAutocomplete(element) {
            element.autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: baseUrl + '/api/search.php',
                        dataType: "json",
                        data: {
                            term: request.term,
                            type: 'product',
                            context: 'purchase'
                        },
                        success: function(data) {
                            console.log('Received data:', data); // Log de datos recibidos
                            response($.map(data, function(item) {
                                return {
                                    label: item.name + ' - $' + (item.price || 'N/A'),
                                    value: item.name,
                                    id: item.id,
                                    price: item.price
                                };
                            }));
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en la búsqueda de productos:", status, error);
                            response([]);
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {
                    const row = $(this).closest('.product-item');
                    row.find('.product-id').val(ui.item.id);
                    row.find('.price').val(ui.item.price || '');
                    updateTotal();
                }
            });
        }

        function initializeProductRow(row) {
            initProductAutocomplete(row.find('.product-search'));
            row.find('.quantity').on('input', updateTotal);
        }
        
        // Calcular subtotal para una fila de producto
    function calculateSubtotal(productItem) {
        const quantity = parseFloat(productItem.find('.quantity').val()) || 0;
        const price = parseFloat(productItem.find('.price').val()) || 0;
        const subtotal = quantity * price;
        productItem.find('.subtotal').val(subtotal.toFixed(2));
        return subtotal;
    }

    // Calcular subtotal y total
    function calculateSubtotalAndTotal() {
        let total = 0;
        $('.product-item').each(function() {
            total += calculateSubtotal($(this));
        });
        $('#total_amount').val(total.toFixed(2));
    }

    // Recalcular cuando se cambie la cantidad o el precio
    $('#productList').on('input', '.quantity, .price', function() {
        calculateSubtotalAndTotal();
    });

        addProductBtn.click(function() {
            const newProduct = productList.children().first().clone();
            newProduct.find('input').val('');
            const inputs = newProduct.find('input, select');
            inputs.each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace('[0]', `[${productCount}]`));
                }
            });
            productList.append(newProduct);
            initializeProductRow(newProduct);
            productCount++;
        });

        productList.on('click', '.remove-product', function() {
            if (productList.children().length > 1) {
                $(this).closest('.product-item').remove();
                updateTotal();
            } else {
                alert('Debe haber al menos un producto en la compra.');
            }
        });

        $('.product-item').each(function() {
            initializeProductRow($(this));
        });

        form.on('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });
})(jQuery);