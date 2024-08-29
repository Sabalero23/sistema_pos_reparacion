<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/supplier_functions.php';


if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('suppliers_create')) {
    $_SESSION['flash_message'] = "No tienes permiso para importar proveedores.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $data = array_map('str_getcsv', file($file));
        $headers = array_shift($data);

        $required_headers = ['name', 'contact_person', 'email', 'phone', 'address'];
        if (array_diff($required_headers, $headers)) {
            $_SESSION['flash_message'] = "El archivo CSV no tiene los encabezados requeridos.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('import_suppliers.php'));
            exit;
        }

        $suppliers = [];
        foreach ($data as $row) {
            $supplier = array_combine($headers, $row);
            $suppliers[] = $supplier;
        }

        $result = importSuppliersFromCSV($suppliers);
        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
            header('Location: ' . url('suppliers.php'));
            exit;
        } else {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('import_suppliers.php'));
            exit;
        }
    } else {
        $_SESSION['flash_message'] = "No se ha seleccionado ningún archivo CSV.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('import_suppliers.php'));
        exit;
    }
}

$pageTitle = "Importar Proveedores";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Importar Proveedores desde CSV</h1>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="<?php echo url('import_suppliers.php'); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Selecciona el archivo CSV</label>
                    <input type="file" class="form-control-file" id="csv_file" name="csv_file" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="<?php echo url('proveedores.csv'); ?>" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Descargar Plantilla CSV
                    </a>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>