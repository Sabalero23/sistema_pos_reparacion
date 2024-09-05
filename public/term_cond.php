<?php
// Incluir el archivo de configuración
require_once __DIR__ . '/../config/config.php';
$pageTitle = "Términos y Condiciones";
require_once __DIR__ . '/../includes/header.php';
$termsAndConditions = <<<EOD
# Términos y Condiciones de Servicio - {$settings['app_name']}

## 1. Plazo de Retiro y Abandono de Productos

1.1 El cliente dispone de un plazo de noventa (90) días calendario, contados a partir de la fecha de recepción del producto por parte de {$settings['app_name']}, para retirar el mismo de nuestras instalaciones.

1.2 Transcurrido este plazo sin que el producto haya sido retirado, se considerará abandonado de conformidad con los artículos 2375, 2525 y 2526 del Código Civil vigente.

1.3 En caso de abandono, {$settings['app_name']} queda facultada para disponer del producto de la manera que considere apropiada, sin obligación de notificación previa al cliente.

## 2. Garantía de Reparación

2.1 {$settings['app_name']} ofrece una garantía de treinta (30) días calendario sobre las reparaciones efectuadas, contados a partir de la fecha de entrega del producto al cliente.

2.2 Esta garantía cubre tanto la mano de obra como los materiales empleados en la reparación.

2.3 La garantía es válida únicamente para el defecto específico reparado y bajo condiciones normales de uso del producto.

## 3. Limitaciones de la Garantía

La garantía no cubre defectos o daños originados por:

3.1 Transporte o acarreo del producto.
3.2 Incendios, inundaciones, tormentas eléctricas u otros desastres naturales.
3.3 Golpes, caídas o accidentes de cualquier naturaleza.
3.4 Uso inadecuado o fuera de las especificaciones del fabricante.
3.5 Desgaste normal de componentes.

## 4. Anulación de la Garantía

4.1 La garantía quedará automáticamente anulada si el producto es intervenido, manipulado o reparado por terceros no autorizados por {$settings['app_name']}.

4.2 Cualquier alteración o remoción de los sellos de garantía también anulará la misma.

## 5. Procedimiento de Reclamo de Garantía

5.1 Para hacer efectiva la garantía, el cliente deberá presentar el comprobante de servicio original en cualquiera de nuestras sucursales dentro del período de garantía.

5.2 {$settings['app_name']} se reserva el derecho de evaluar el producto para determinar si el defecto está cubierto por la garantía.

## 6. Limitación de Responsabilidad

6.1 La responsabilidad máxima de {$settings['app_name']} bajo esta garantía se limita al costo de la reparación efectuada.

6.2 {$settings['app_name']} no será responsable por daños indirectos, incidentales o consecuentes derivados del uso o imposibilidad de uso del producto.

## 7. Ley Aplicable y Jurisdicción

7.1 Estos términos y condiciones se regirán e interpretarán de acuerdo con las leyes vigentes en la República Argentina, incluyendo, pero no limitado a, los artículos 2375, 2525 y 2526 del Código Civil y Comercial de la Nación.

7.2 Cualquier disputa que surja en relación con estos términos y condiciones estará sujeta a la jurisdicción exclusiva de los tribunales competentes de la ciudad donde se encuentre la sucursal de {$settings['app_name']} que haya prestado el servicio.

{$settings['app_name']} se reserva el derecho de modificar estos términos y condiciones en cualquier momento. Las modificaciones entrarán en vigor a partir de su publicación en nuestro sitio web o local comercial.

Última actualización: " . 30/08/2024') . "

Para cualquier consulta relacionada con estos términos y condiciones, por favor contacte a: {$settings['admin_email']}
EOD;

// Función para mostrar los términos y condiciones
function displayTermsAndConditions() {
    global $termsAndConditions;
    echo nl2br(htmlspecialchars($termsAndConditions));
}

// Si este archivo se accede directamente, muestra los términos y condiciones
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Términos y Condiciones - <?php echo htmlspecialchars(APP_NAME); ?></title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
            h1 { color: #333; }
            h2 { color: #666; }
        </style>
    </head>
    <body>
        <?php displayTermsAndConditions(); ?>
    </body>
    </html>
    <?php
}

require_once __DIR__ . '/../includes/footer.php';

?>