(function($) {
    $(function() {
        if (typeof $.ui === 'undefined' || typeof $.ui.autocomplete === 'undefined') {
            console.error('jQuery UI or autocomplete widget is not loaded');
            return;
        }

        const form = $('#saleForm');
        const productList = $('#productList');
        const addProductBtn = $('#addProduct');
        let productCount = 1;

        console.log('URL de la API:', baseUrl + '/api/search.php');

        $('#customer_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: baseUrl + '/api/search.php',
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
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la búsqueda de clientes:", status, error);
                        response([]);
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                $('#customer_id').val(ui.item.id);
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
                            type: 'product'
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.name + ' - $' + item.price,
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
                    row.find('.price').val(ui.item.price);
                    updateSubtotal(row);
                }
            });
        }

        function initializeProductRow(row) {
            initProductAutocomplete(row.find('.product-search'));
            row.find('.quantity, .price').on('input', function() { updateSubtotal(row); });
        }

        function updateSubtotal(row) {
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const prc = parseFloat(row.find('.price').val()) || 0;
            row.find('.subtotal').text((qty * prc).toFixed(2));
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).text()) || 0;
            });
            $('#total_amount').val(total.toFixed(2));
        }

        addProductBtn.click(function() {
            const newProduct = productList.children().first().clone();
            newProduct.find('input').val('');
            newProduct.find('.subtotal').text('0.00');
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
                alert('Debe haber al menos un producto en la venta.');
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