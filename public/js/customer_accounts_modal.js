document.addEventListener('DOMContentLoaded', function() {
    if (clientsWithIssues && clientsWithIssues.length > 0) {
        const modalElement = document.getElementById('clientIssuesModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        const modalBody = modalElement.querySelector('.modal-body');
        const closeButton = modalElement.querySelector('.btn-close');

        let content = '<div class="table-responsive"><table class="table table-striped table-sm">';
        content += '<thead><tr><th>Cliente</th><th>Saldo</th><th>Vencimiento</th><th>Monto</th><th>Estado</th></tr></thead>';
        content += '<tbody>';

        clientsWithIssues.forEach(client => {
            const statusClass = client.status === 'vencida' ? 'text-danger' : 'text-warning';
            content += `<tr>
                <td>${client.name}</td>
                <td>$${parseFloat(client.balance).toFixed(2)}</td>
                <td>${new Date(client.due_date).toLocaleDateString()}</td>
                <td>$${parseFloat(client.amount).toFixed(2)}</td>
                <td class="${statusClass}">${client.status.charAt(0).toUpperCase() + client.status.slice(1)}</td>
            </tr>`;
        });

        content += '</tbody></table></div>';
        modalBody.innerHTML = content;

        // Mostrar el modal
        modal.show();

        // Manejar el cierre del modal
        closeButton.addEventListener('click', function() {
            modal.hide();
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
});