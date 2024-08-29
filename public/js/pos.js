(function($) {
    $(function() {
        // Verificar si jQuery UI y el widget de autocompletado están cargados
        if (typeof $.ui === 'undefined' || typeof $.ui.autocomplete === 'undefined') {
            console.error('jQuery UI or autocomplete widget is not loaded');
            return;
        }

        // Elementos del DOM
        const productList = $('#productList');
        const addProductBtn = $('#addProduct');
        const processSaleBtn = $('#processSale');
        const applyTaxesCheckbox = $('#apply_taxes');
        const customerSearch = $('#customer_search');
        const customerId = $('#customer_id');
        const paymentMethod = $('#payment_method');
        const subtotalElement = $('#subtotal');
        const taxesElement = $('#taxes');
        const totalElement = $('#total');

        let productCount = 1;
        const TAX_RATE = 0.21; // 21% tax rate

        // Inicializar autocompletado para la búsqueda de clientes
        customerSearch.autocomplete({
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
                customerId.val(ui.item.id);
            }
        });

        // Inicializar autocompletado para la búsqueda de productos
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
                    updateTotals();
                }
            });
        }

        // Inicializar una fila de producto
        function initializeProductRow(row) {
            initProductAutocomplete(row.find('.product-search'));
            row.find('.quantity, .price').on('input', function() { 
                updateSubtotal(row);
                updateTotals();
            });
            row.find('.remove-product').on('click', function() {
                if (productList.children().length > 1) {
                    row.remove();
                    updateTotals();
                } else {
                    alert('Debe haber al menos un producto en la venta.');
                }
            });
        }

        // Actualizar el subtotal de una fila de producto
        function updateSubtotal(row) {
            const price = parseFloat(row.find('.price').val()) || 0;
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const subtotal = price * qty;
            row.find('.subtotal').text(subtotal.toFixed(2));
        }

        // Actualizar los totales de la venta
        function updateTotals() {
            let subtotal = 0;
            $('.subtotal').each(function() {
                subtotal += parseFloat($(this).text()) || 0;
            });
            const applyTaxes = applyTaxesCheckbox.prop('checked');
            const taxes = applyTaxes ? subtotal * TAX_RATE : 0;
            const total = subtotal + taxes;

            subtotalElement.text(subtotal.toFixed(2));
            taxesElement.text(taxes.toFixed(2));
            totalElement.text(total.toFixed(2));
        }

        // Agregar un nuevo producto a la venta
        addProductBtn.on('click', function() {
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

        // Inicializar las filas de producto existentes
        $('.product-item').each(function() {
            initializeProductRow($(this));
        });

        // Manejar el cambio en el checkbox de impuestos
        applyTaxesCheckbox.on('change', updateTotals);

        // Procesar la venta
        processSaleBtn.on('click', function() {
            const saleData = {
                customer_id: $('#customer_id').val(),
                payment_method: $('#payment_method').val(),
                items: [],
                total_amount: parseFloat($('#total').text()),
                apply_taxes: $('#apply_taxes').prop('checked'),
                taxes_amount: parseFloat($('#taxes').text())
            };

            $('.product-item').each(function() {
                const productId = $(this).find('.product-id').val();
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                const price = parseFloat($(this).find('.price').val()) || 0;

                if (productId && quantity > 0) {
                    saleData.items.push({
                        product_id: productId,
                        quantity: quantity,
                        price: price
                    });
                }
            });

            if (saleData.items.length === 0) {
                alert('Debe agregar al menos un producto a la venta.');
                return;
            }

            if (!saleData.customer_id) {
                alert('Debe seleccionar un cliente para la venta.');
                return;
            }

            if (!saleData.payment_method) {
                alert('Debe seleccionar un método de pago para la venta.');
                return;
            }

            console.log('Datos de la venta:', saleData);

            $.ajax({
                url: baseUrl + '/api/create_sale.php',
                method: 'POST',
                data: JSON.stringify(saleData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Venta procesada exitosamente.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            window.location.href = baseUrl + '/sales.php?action=view&id=' + response.sale_id;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la venta: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al procesar la venta:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la venta. Por favor, inténtalo de nuevo.'
                    });
                }
            });
        });
        
        // Inicializar totales
        updateTotals();
    });
})(jQuery);