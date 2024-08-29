jQuery.noConflict();
(function($) {
    $(document).ready(function() {
        if (typeof $.ui === 'undefined' || typeof $.ui.autocomplete === 'undefined') {
            console.error('jQuery UI or autocomplete widget is not loaded');
            return;
        }

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
    });
})(jQuery);