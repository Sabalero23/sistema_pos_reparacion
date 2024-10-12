(function() {
    // Variable global para controlar si el modal ya se ha mostrado
    window.modalShown = false;

    function createAndShowModal() {
        if (window.modalShown) return;

        var modalElement = document.getElementById('clientIssuesModal');
        if (!modalElement) {
            console.error('Modal element not found');
            return;
        }

        var modal = new bootstrap.Modal(modalElement);
        var modalBody = modalElement.querySelector('.modal-body');

        modalBody.innerHTML = '';

        if (typeof clientsWithIssues === 'undefined' || !Array.isArray(clientsWithIssues)) {
            modalBody.innerHTML = '<p>Error: No se pudieron cargar los datos de los clientes.</p>';
            console.error('clientsWithIssues is undefined or not an array');
        } else if (clientsWithIssues.length === 0) {
            modalBody.innerHTML = '<p>No hay clientes con cuotas vencidas o próximas.</p>';
        } else {
            try {
                var table = document.createElement('table');
                table.className = 'table table-striped';
                var thead = document.createElement('thead');
                var tr = document.createElement('tr');
                ['Cliente', 'Cuotas Vencidas', 'Próximo Vencimiento'].forEach(function (header) {
                    var th = document.createElement('th');
                    th.textContent = header || '';
                    tr.appendChild(th);
                });
                thead.appendChild(tr);
                table.appendChild(thead);

                var tbody = document.createElement('tbody');
                clientsWithIssues.forEach(function (client) {
                    if (client && typeof client === 'object') {
                        var tr = document.createElement('tr');
                        [
                            client.name || 'N/A',
                            (client.overdue_installments !== undefined && client.overdue_installments !== null) ? client.overdue_installments : 'N/A',
                            client.next_due_date || 'N/A'
                        ].forEach(function (value, index) {
                            var td = document.createElement('td');
                            if (index === 2 && value !== 'N/A') {
                                try {
                                    td.textContent = new Date(value).toLocaleDateString();
                                } catch (e) {
                                    td.textContent = 'Fecha inválida';
                                }
                            } else {
                                td.textContent = String(value);
                            }
                            tr.appendChild(td);
                        });
                        tbody.appendChild(tr);
                    }
                });
                table.appendChild(tbody);

                modalBody.appendChild(table);
            } catch (error) {
                console.error('Error al crear la tabla:', error);
                modalBody.innerHTML = '<p>Error al procesar los datos de los clientes.</p>';
            }
        }

        modal.show();
        window.modalShown = true;

        // Eliminar el modal del DOM después de cerrarlo
        modalElement.addEventListener('hidden.bs.modal', function () {
            modalElement.remove();
        }, { once: true });
    }

    // Intentar mostrar el modal inmediatamente
    createAndShowModal();

    // Sobrescribir métodos que podrían usarse para abrir el modal
    window.openModal = function() { 
        console.log('El modal ya se ha mostrado y no se volverá a abrir.');
    };

    // Reemplazar el método show del modal para evitar que se abra de nuevo
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var originalShow = bootstrap.Modal.prototype.show;
        bootstrap.Modal.prototype.show = function() {
            if (this._element.id === 'clientIssuesModal' && window.modalShown) {
                console.log('El modal ya se ha mostrado y no se volverá a abrir.');
                return;
            }
            originalShow.apply(this, arguments);
        };
    }

    // Detener cualquier intervalo existente
    var highestTimeoutId = setTimeout(";");
    for (var i = 0 ; i < highestTimeoutId ; i++) {
        clearTimeout(i); 
    }
})();