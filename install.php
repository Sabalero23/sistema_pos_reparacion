<?php
// Función para verificar los requisitos del sistema
function checkRequirements() {
    $requirements = [
        'PHP Version' => ['required' => '7.4.0', 'current' => PHP_VERSION],
        'PDO Extension' => ['required' => true, 'current' => extension_loaded('pdo')],
        'PDO MySQL Extension' => ['required' => true, 'current' => extension_loaded('pdo_mysql')],
        'Writeable config.php' => ['required' => true, 'current' => is_writable(__DIR__ . '/config/config.php')]
    ];

    $allMet = true;
    foreach ($requirements as $requirement => $values) {
        if ($requirement === 'PHP Version') {
            if (version_compare($values['current'], $values['required'], '<')) {
                $allMet = false;
            }
        } elseif ($values['current'] !== $values['required']) {
            $allMet = false;
        }
    }

    return ['met' => $allMet, 'details' => $requirements];
}

// Función para actualizar config.php
function updateConfig($host, $dbname, $username, $password, $baseUrl) {
    if (substr($baseUrl, -7) !== '/public') {
        $baseUrl = rtrim($baseUrl, '/') . '/public';
    }

    $configFile = __DIR__ . '/config/config.php';
    $configContent = file_get_contents($configFile);

    $replacements = [
        "/define\('DB_HOST',\s*'.*?'\);/" => "define('DB_HOST', '$host');",
        "/define\('DB_NAME',\s*'.*?'\);/" => "define('DB_NAME', '$dbname');",
        "/define\('DB_USER',\s*'.*?'\);/" => "define('DB_USER', '$username');",
        "/define\('DB_PASS',\s*'.*?'\);/" => "define('DB_PASS', '$password');",
        "/define\('BASE_URL',\s*'.*?'\);/" => "define('BASE_URL', '$baseUrl');",
    ];

    foreach ($replacements as $pattern => $replacement) {
        $configContent = preg_replace($pattern, $replacement, $configContent);
    }
    return file_put_contents($configFile, $configContent) !== false;
}

// Función para ejecutar el script SQL
function executeSQLScript($host, $dbname, $username, $password) {
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo->exec("USE `$dbname`");

        $sql = file_get_contents(__DIR__ . '/database.sql');
        $statements = explode(';', $sql);
        $totalStatements = count($statements);

        $response = [];
        foreach ($statements as $index => $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
                $progress = round(($index + 1) / $totalStatements * 100);
                $response[] = ['progress' => $progress];
            }
        }
        $response[] = ['success' => true];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        return true;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        return false;
    }
}
$message = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'welcome';
$requirements = checkRequirements();

// Manejo de POST para asegurar una respuesta JSON válida
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $requiredFields = ['db_host', 'db_name', 'db_user', 'db_pass', 'db_baseUrl'];
    $missingFields = array_filter($requiredFields, function($field) {
        return !isset($_POST[$field]) || $_POST[$field] === '';
    });

    if (empty($missingFields)) {
        $host = $_POST['db_host'];
        $dbname = $_POST['db_name'];
        $username = $_POST['db_user'];
        $password = $_POST['db_pass'];
        $baseUrl = $_POST['db_baseUrl'];

        if (updateConfig($host, $dbname, $username, $password, $baseUrl)) {
            executeSQLScript($host, $dbname, $username, $password);
        } else {
            echo json_encode(['error' => "Error al actualizar el archivo de configuración."]);
        }
    } else {
        echo json_encode(['error' => "Faltan los siguientes campos: " . implode(', ', $missingFields)]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación del Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Roboto Mono', monospace;
            background-color: #1e1e1e;
            color: #e0e0e0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .console {
            background-color: #2d2d2d;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .console-output {
            height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #1e1e1e;
            border-radius: 3px;
        }
        .console-input {
            display: flex;
        }
        .console-input input {
            flex-grow: 1;
            background-color: #3c3c3c;
            border: none;
            color: #e0e0e0;
            padding: 10px;
            font-family: 'Roboto Mono', monospace;
        }
        .console-input button {
            background-color: #0078d4;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color: 0.3s;
        }
        .console-input button:hover {
            background-color: #005a9e;
        }
        .command {
            color: #4ec9b0;
        }
        .output {
            color: #d7ba7d;
        }
        .error {
            color: #f44747;
        }
        .success {
            color: #6a9955;
        }
        #progressBar {
            width: 100%;
            background-color: #3c3c3c;
            border-radius: 5px;
            margin-top: 20px;
            display: none;
        }
        #progressBar .progress {
            width: 0%;
            height: 30px;
            background-color: #0078d4;
            border-radius: 5px;
            transition: width 0.5s;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalación del Sistema</h1>
        <div class="console">
            <div id="consoleOutput" class="console-output"></div>
            <div class="console-input">
                <input type="text" id="consoleInput" placeholder="Ingrese los datos solicitados...">
                <button id="submitButton">Enviar</button>
            </div>
            <div id="progressBar"><div class="progress"></div></div>
        </div>
    </div>

    <script>
    const consoleOutput = document.getElementById('consoleOutput');
    const consoleInput = document.getElementById('consoleInput');
    const submitButton = document.getElementById('submitButton');
    const progressBar = document.getElementById('progressBar');
    const progress = progressBar.querySelector('.progress');

    function addConsoleText(text, className = '') {
        const p = document.createElement('p');
        p.innerHTML = text;
        p.className = className;
        consoleOutput.appendChild(p);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }

    const installSteps = [
    { command: 'iniciar_instalacion', output: 'Iniciando el proceso de instalación...' },
    { command: 'verificar_requisitos', output: 'Verificando requisitos del sistema...' },
    { command: 'ingresar_host_db', output: 'Ingrese el host de la base de datos (por defecto, localhost):' },
    { command: 'ingresar_nombre_db', output: 'Ingrese el nombre de la base de datos:' },
    { command: 'ingresar_usuario_db', output: 'Ingrese el usuario de la base de datos:' },
    { command: 'ingresar_password_db', output: 'Ingrese la contraseña de la base de datos:' },
    { command: 'ingresar_base_url', output: 'Ingrese la URL base del sistema:' },
    { command: 'configurar_base_datos', output: 'Configurando la base de datos...' },
    { command: 'ejecutar_migraciones', output: 'Ejecutando migraciones de la base de datos...' },
    { command: 'finalizar_instalacion', output: 'Finalizando la instalación...' }
];

let currentStep = 0;
let dbConfig = {};

    function processNextStep() {
        if (currentStep < installSteps.length) {
            const step = installSteps[currentStep];
            addConsoleText(`> ${step.command}`, 'command');

            if (step.command.startsWith('ingresar_')) {
                consoleInput.focus();
            } else {
                addConsoleText(step.output, 'output');
                currentStep++;
                consoleInput.value = '';

                if (currentStep === installSteps.length) {
                    submitDatabaseConfig();
                } else {
                    setTimeout(processNextStep, 1000);
                }
            }
        }
    }

    function handleInput() {
    const input = consoleInput.value.trim();
    if (input) {
        addConsoleText(input, 'input');

        switch (installSteps[currentStep].command) {
            case 'ingresar_host_db':
                dbConfig.host = input || 'localhost';
                break;
            case 'ingresar_nombre_db':
                dbConfig.name = input;
                break;
            case 'ingresar_usuario_db':
                dbConfig.user = input;
                break;
            case 'ingresar_password_db':
                dbConfig.pass = input;
                break;
            case 'ingresar_base_url':
                dbConfig.baseUrl = input;
                break;
        }

        currentStep++;
        consoleInput.value = '';
        processNextStep();
    }
}

function submitDatabaseConfig() {
    progressBar.style.display = 'block';
    const formData = new FormData();
    for (const [key, value] of Object.entries(dbConfig)) {
        formData.append(`db_${key}`, value);
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        data.forEach(item => {
            if (item.progress) {
                progress.style.width = `${item.progress}%`;
            } else if (item.success) {
                Swal.fire({
                    title: '¡Instalación Completada!',
                    text: 'El sistema se ha instalado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Ir al Sistema'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = dbConfig.baseUrl;
                    }
                });
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error durante la instalación: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

    function handleEnterKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleInput();
        }
    }

    consoleInput.addEventListener('keypress', handleEnterKey);
    submitButton.addEventListener('click', handleInput);

    addConsoleText('Bienvenido al instalador del Sistema.', 'output');
    addConsoleText('Presione Enter o haga clic en Enviar para comenzar la instalación...', 'output');

    function startInstall(e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.key === 'Enter')) {
            e.preventDefault();
            consoleInput.removeEventListener('keypress', startInstall);
            submitButton.removeEventListener('click', startInstall);
            processNextStep();
        }
    }

    consoleInput.addEventListener('keypress', startInstall);
    submitButton.addEventListener('click', startInstall);
    </script>
</body>
</html>