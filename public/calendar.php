<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/home_visit_functions.php';

if (!isLoggedIn() || !hasPermission('calendar_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$pageTitle = "Calendario de Visitas a Domicilio";
require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* Estilo para el fondo de las celdas de los días */
.fc .fc-daygrid-day-frame {
    position: relative;
    min-height: 100%;
    background-color: #f8f9fa; /* Un gris muy claro para el fondo */
}

/* Estilo para los eventos */
.fc-event {
    border: 1px solid #3498db;
    background-color: #3498db;
    color: white;
    padding: 2px;
    cursor: pointer;
    margin-bottom: 1px; /* Espacio entre eventos */
}

.fc-daygrid-event {
    white-space: normal;
    align-items: normal;
    font-size: 0.8em;
}

/* Mejorar la visibilidad de los eventos en la vista de mes */
.fc-daygrid-day-events {
    margin-top: 2px;
}

/* Estilo para el texto del día */
.fc-daygrid-day-number {
    font-weight: bold;
    color: #333;
}

/* Estilo para días del fin de semana */
.fc-day-sat, .fc-day-sun {
    background-color: #e9ecef; /* Un gris un poco más oscuro para fines de semana */
}

/* Estilo para el día actual */
.fc-day-today {
    background-color: #e3f2fd !important; /* Un azul muy claro para el día actual */
}

/* Estilo para mejorar la visibilidad de los eventos cuando hay muchos */
.fc-daygrid-more-link {
    color: #007bff;
    font-weight: bold;
}

/* Estilo para el encabezado del calendario */
.fc-header-toolbar {
    margin-bottom: 1em;
    padding: 0.5em;
    background-color: #f1f3f5;
    border-radius: 5px;
}
</style>

<div class="container mt-4">
    <h1 class="mb-4">Calendario de Visitas a Domicilio</h1>
    <div id="calendar"></div>
</div>

<!-- Incluir los estilos y scripts de FullCalendar v5 -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales/es.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        locale: 'es',
        events: {
            url: '<?php echo url('api/calendar_events.php'); ?>',
            failure: function() {
                console.error('Error al cargar los eventos');
            }
        },
        eventColor: '#3498db', // Color de fondo del evento
        eventTextColor: 'white', // Color del texto del evento
        eventBackgroundColor: '#3498db', // Asegurarse de que el color de fondo esté establecido
        eventBorderColor: '#2980b9', // Color del borde del evento
        displayEventTime: true, // Mostrar la hora del evento
        eventDisplay: 'block', // Mostrar eventos como bloques
        loading: function(isLoading) {
            if (isLoading) {
                console.log('Cargando eventos...');
            } else {
                console.log('Eventos cargados');
                // Imprimir todos los eventos cargados para depuración
                var allEvents = calendar.getEvents();
                console.log('Eventos cargados:', allEvents);
            }
        },
        eventDidMount: function(info) {
            console.log('Evento montado:', info.event.title, info.event.start);
        },
        eventClick: function(info) {
            console.log('Evento clickeado:', info.event.title, info.event.url);
            if (info.event.url) {
                window.location.href = info.event.url;
                return false;
            }
        }
    });
    calendar.render();
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>