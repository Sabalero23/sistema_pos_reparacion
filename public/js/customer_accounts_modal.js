document.addEventListener('DOMContentLoaded', function () {
    var modal = new bootstrap.Modal(document.getElementById('clientIssuesModal'));
    var modalBody = document.querySelector('#clientIssuesModal .modal-body');

    function openModal() {
        modalBody.innerHTML = '';

        if (clientsWithIssues.length === 0) {
            modalBody.innerHTML = '<p>No hay clientes con cuotas vencidas o próximas.</p>';
        } else {
            var table = document.createElement('table');
            table.className = 'table table-striped';
            var thead = document.createElement('thead');
            var tr = document.createElement('tr');
            ['Cliente', 'Cuotas Vencidas', 'Próximo Vencimiento'].forEach(function (header) {
                var th = document.createElement('th');
                th.textContent = header;
                tr.appendChild(th);
            });
            thead.appendChild(tr);
            table.appendChild(thead);

            var tbody = document.createElement('tbody');
            clientsWithIssues.forEach(function (client) {
                var tr = document.createElement('tr');
                [client.name, client.overdue_installments, client.next_due_date].forEach(function (value, index) {
                    var td = document.createElement('td');
                    td.textContent = value !== null ? value : 'N/A';
                    if (index === 2 && value !== null) {
                        td.textContent = new Date(value).toLocaleDateString();
                    }
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);

            modalBody.appendChild(table);
        }

        modal.show();
    }

    setInterval(openModal, 5000);
});