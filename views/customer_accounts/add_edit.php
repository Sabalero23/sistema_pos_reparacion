<?php
// Asegúrate de que todas las variables necesarias estén definidas
$isEdit = isset($account);
$pageTitle = $isEdit ? 'Editar Cuenta de Cliente' : 'Añadir Cuenta de Cliente';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $pageTitle; ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?action=' . ($isEdit ? 'edit&id=' . $account['id'] : 'add')); ?>" method="post" class="needs-validation" novalidate>         
            <div class="mb-3">
                <label for="customer_search" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente..." required <?php echo $isEdit ? 'disabled' : ''; ?> value="<?php echo $isEdit ? htmlspecialchars($account['customer_name']) : ''; ?>">
                <input type="hidden" id="customer_id" name="customer_id" required value="<?php echo $isEdit ? htmlspecialchars($account['customer_id']) : ''; ?>">
                <div class="invalid-feedback">
                    Por favor seleccione un cliente.
                </div>
            </div>
            
            <div class="mb-3">
                <label for="total_amount" class="form-label">Monto Total</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" min="0.01" required value="<?php echo $isEdit ? htmlspecialchars($account['total_amount']) : ''; ?>">
                </div>
                <div class="invalid-feedback">
                    Por favor ingrese un monto total válido mayor a cero.
                </div>
            </div>

            <div class="mb-3">
                <label for="down_payment" class="form-label">Entrega Inicial</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="down_payment" name="down_payment" step="0.01" min="0" required value="<?php echo $isEdit ? htmlspecialchars($account['down_payment']) : '0'; ?>">
                </div>
                <div class="invalid-feedback">
                    Por favor ingrese un monto de entrega inicial válido.
                </div>
            </div>

            <div class="mb-3">
                <label for="num_installments" class="form-label">Número de Cuotas</label>
                <input type="number" class="form-control" id="num_installments" name="num_installments" min="1" required value="<?php echo $isEdit ? htmlspecialchars($account['num_installments']) : '1'; ?>">
                <div class="invalid-feedback">
                    Por favor ingrese un número válido de cuotas.
                </div>
            </div>

            <div class="mb-3">
                <label for="first_due_date" class="form-label">Fecha de Vencimiento de la Primera Cuota</label>
                <input type="date" class="form-control" id="first_due_date" name="first_due_date" required value="<?php echo $isEdit ? htmlspecialchars($account['first_due_date']) : ''; ?>">
                <div class="invalid-feedback">
                    Por favor seleccione la fecha de vencimiento de la primera cuota.
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $isEdit ? htmlspecialchars($account['description']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Actualizar' : 'Crear'; ?> Cuenta</button>
            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function loadJQueryUI(callback) {
        var script = document.createElement("script");
        script.src = "https://code.jquery.com/ui/1.12.1/jquery-ui.min.js";
        script.onload = callback;
        document.head.appendChild(script);
    }

    $(document).ready(function() {
        loadJQueryUI(function() {
            if($.fn.autocomplete) {
                console.log('jQuery UI Autocomplete está disponible');
                $("#customer_search").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "<?php echo htmlspecialchars(url('api/search.php')); ?>",
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
            } else {
                console.error('jQuery UI Autocomplete no está disponible');
            }
        });

        // Validación del formulario
        $('form').on('submit', function(event) {
            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // Cálculo automático del saldo
        $('#total_amount, #down_payment').on('input', function() {
            var totalAmount = parseFloat($('#total_amount').val()) || 0;
            var downPayment = parseFloat($('#down_payment').val()) || 0;
            var balance = totalAmount - downPayment;
            $('#balance').val(balance.toFixed(2));
        });

        // Validación de la entrega inicial
        $('#down_payment').on('input', function() {
            var totalAmount = parseFloat($('#total_amount').val()) || 0;
            var downPayment = parseFloat($(this).val()) || 0;
            
            if (downPayment > totalAmount) {
                $(this).val(totalAmount.toFixed(2));
            }
        });

        // Validación de la fecha de vencimiento
        $('#first_due_date').on('change', function() {
            var selectedDate = new Date($(this).val());
            var today = new Date();
            
            if (selectedDate < today) {
                alert('La fecha de vencimiento no puede ser anterior a hoy.');
                $(this).val('');
            }
        });

        // Cálculo automático de cuotas
        $('#total_amount, #down_payment, #num_installments').on('input', function() {
            var totalAmount = parseFloat($('#total_amount').val()) || 0;
            var downPayment = parseFloat($('#down_payment').val()) || 0;
            var numInstallments = parseInt($('#num_installments').val()) || 1;
            
            var balance = totalAmount - downPayment;
            var installmentAmount = balance / numInstallments;
            
            $('#installment_amount').text(installmentAmount.toFixed(2));
        });
    });
    </script>
</body>
</html>