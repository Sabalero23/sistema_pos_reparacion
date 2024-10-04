<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajustamos las rutas para que sean relativas al directorio actual
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/utils.php';

// Verificar si la tienda online está activa
$companyInfo = getCompanyInfo();
$isOnlineStoreEnabled = isset($companyInfo['online_store_enabled']) && $companyInfo['online_store_enabled'] == 1;

if ($isOnlineStoreEnabled) {
    // Si la tienda está activa, incluir el archivo tienda.php
    include __DIR__ . '/public/tienda.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema POS y Gestión de Órdenes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .logo-img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">POS & Servicio</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="jumbotron text-center">
        <h1 class="display-4">Bienvenido a nuestro Sistema POS y Gestión de Órdenes</h1>
        <p class="lead">Una solución integral para la gestión eficiente de tu negocio y servicios de reparación</p>
    </div>

    <div class="container">
        <div id="features" class="row text-center">
            <h2 class="col-12 mb-4">Características Principales</h2>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📊</div>
                <h3>Gestión de Ventas y POS</h3>
                <p>Procesa ventas rápidamente con nuestra interfaz intuitiva de punto de venta.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📦</div>
                <h3>Control de Inventario</h3>
                <p>Mantén un seguimiento preciso de tu stock y recibe alertas de bajo inventario.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">🔧</div>
                <h3>Órdenes de Servicio</h3>
                <p>Gestiona eficientemente las reparaciones y el mantenimiento de equipos.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📝</div>
                <h3>Presupuestos</h3>
                <p>Crea y gestiona presupuestos para tus clientes de manera sencilla.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">👥</div>
                <h3>Gestión de Clientes</h3>
                <p>Mantén un registro detallado de tus clientes y su historial de compras.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-icon">📈</div>
                <h3>Informes y Análisis</h3>
                <p>Obtén insights valiosos con nuestros informes detallados y paneles de control.</p>
            </div>
        </div>

        <div id="about" class="row mt-5">
            <div class="col-md-6">
                <h2>Acerca de Nuestro Sistema</h2>
                <p>Nuestro Sistema POS y Gestión de Órdenes es una solución completa diseñada para negocios que combinan ventas de productos con servicios de reparación. Con una interfaz intuitiva y potentes funcionalidades, te ayudamos a optimizar tus operaciones diarias y mejorar la satisfacción del cliente.</p>
            </div>
            <div class="col-md-6 text-center">
                <img src="/public/uploads/logo_1724901287.png" alt="Logo del Sistema" class="img-fluid rounded logo-img">
            </div>
        </div>

        <div id="contact" class="row mt-5 text-center">
            <div class="col-12">
                <h2>Contacto</h2>
                <p>¿Tienes preguntas? No dudes en contactarnos.</p>
            </div>
            <div class="col-md-6 mx-auto">
                <form>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Tu email">
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="3" placeholder="Tu mensaje"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="public/index.php" class="btn btn-primary btn-lg btn-enter">Entrar al Sistema POS</a>
            <a href="public/seguimiento.php" class="btn btn-info btn-lg btn-enter">Seguimiento de Orden</a>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>© 2024 Sistema POS y Gestión de Órdenes. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>