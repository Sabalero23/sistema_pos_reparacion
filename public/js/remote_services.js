$(document).ready(function() {
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
                    if (data.length === 0) {
                        // No se encontraron clientes, ofrecer crear uno nuevo
                        response([{
                            label: 'Crear nuevo cliente',
                            value: 'new_client'
                        }]);
                    } else {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name,
                                id: item.id
                            };
                        }));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la búsqueda de clientes:", status, error);
                    response([]);
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {
            if (ui.item.value === 'new_client') {
                // Abrir modal para crear nuevo cliente
                $('#newClientModal').modal('show');
            } else {
                $('#customer_id').val(ui.item.id);
            }
        }
    });

    // Configurar la fecha mínima para el campo de fecha
    var today = new Date().toISOString().split('T')[0];
    $("#visit_date").attr('min', today);

    // Configurar el rango de horas para el campo de hora
    $("#visit_time").attr('min', '08:00');
    $("#visit_time").attr('max', '22:00');

    $("#homeVisitForm").on('submit', function(event) {
    if (!this.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    this.classList.add('was-validated');
});

    $('#saveNewClient').on('click', function() {
        var formData = $('#newClientForm').serialize();
        $.ajax({
            url: baseUrl + '/api/create_client.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#newClientModal').modal('hide');
                    $('#customer_search').val(response.client.name);
                    $('#customer_id').val(response.client.id);
                    Swal.fire('Éxito', 'Cliente creado correctamente', 'success');
                } else {
                    Swal.fire('Error', 'No se pudo crear el cliente: ' + response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Ocurrió un error al crear el cliente', 'error');
            }
        });
    });
});