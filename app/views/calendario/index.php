<?php
require_once __DIR__ . '/../../core/Helpers.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../config/db.php';

Auth::requireLogin();

$tenantId = Auth::tenantId();
$isSuper = Auth::isSuperadmin();

$lang = $_SESSION['lang'] ?? 'es';

require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<style>
.calendar-container {
    background: var(--white);
    border: 1px solid var(--slate-100);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.calendar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--slate-100);
}
.calendar-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--slate-900);
    display: flex;
    align-items: center;
    gap: 10px;
}
.calendar-title i {
    color: var(--teal);
}
.calendar-legend {
    display: flex;
    gap: 16px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .75rem;
    color: var(--slate-600);
}
.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.legend-dot.pendiente { background: #f59e0b; }
.legend-dot.en-proceso { background: #3b82f6; }
.legend-dot.completado { background: #10b981; }
.legend-dot.cancelado { background: #ef4444; }

#calendar {
    max-height: 700px;
}
.fc {
    --fc-border-color: var(--slate-100);
    --fc-button-bg-color: var(--teal);
    --fc-button-border-color: var(--teal);
    --fc-button-hover-bg-color: var(--teal-dark);
    --fc-button-hover-border-color: var(--teal-dark);
    --fc-button-active-bg-color: var(--teal-dark);
    --fc-button-active-border-color: var(--teal-dark);
    --fc-today-bg-color: rgba(11,168,150,0.08);
    --fc-event-border-color: transparent;
}
.fc .fc-toolbar-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.1rem;
    color: var(--slate-800);
}
.fc .fc-button {
    font-size: .8rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
}
.fc .fc-daygrid-day-number {
    font-size: .85rem;
    color: var(--slate-600);
}
.fc .fc-col-header-cell-cushion {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--slate-500);
    padding: 10px 0;
}
.fc-event {
    cursor: pointer;
    border-radius: 6px;
    padding: 2px 6px;
    font-size: .75rem;
    font-weight: 500;
}
.fc-event:hover {
    filter: brightness(0.95);
}

.event-programado { background: #f59e0b; color: #fff; }
.event-en-proceso { background: #3b82f6; color: #fff; }
.event-cerrado { background: #10b981; color: #fff; }
.event-anulado { background: #ef4444; color: #fff; }

.filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.filter-btn {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 600;
    border: 1px solid var(--slate-200);
    background: var(--white);
    color: var(--slate-600);
    cursor: pointer;
    transition: all .2s;
}
.filter-btn:hover {
    border-color: var(--teal);
    color: var(--teal);
}
.filter-btn.active {
    background: var(--teal);
    border-color: var(--teal);
    color: #fff;
}

.modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1040;
    display: none;
}
.modal-backdrop-custom.show {
    display: block;
}
.modal-custom {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--white);
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    z-index: 1050;
    display: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal-custom.show {
    display: block;
}
.modal-custom-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-custom-title {
    font-family: 'Sora', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--slate-900);
}
.modal-custom-close {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: var(--slate-50);
    color: var(--slate-500);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .2s;
}
.modal-custom-close:hover {
    background: var(--slate-200);
    color: var(--slate-700);
}
.modal-custom-body {
    padding: 20px;
    max-height: 60vh;
    overflow-y: auto;
}
.modal-custom-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--slate-100);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.detail-row {
    display: flex;
    margin-bottom: 12px;
}
.detail-row:last-child {
    margin-bottom: 0;
}
.detail-label {
    width: 100px;
    font-size: .75rem;
    font-weight: 600;
    color: var(--slate-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.detail-value {
    flex: 1;
    font-size: .9rem;
    color: var(--slate-800);
}
.detail-value.status-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 100px;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
}
.status-programado { background: #fef3c7; color: #b45309; }
.status-en-proceso { background: #dbeafe; color: #1d4ed8; }
.status-cerrado { background: #d1fae5; color: #047857; }
.status-anulado { background: #fee2e2; color: #b91c1c; }
</style>

<div class="calendar-container">
    <div class="calendar-header">
        <div class="calendar-title">
            <i class="fas fa-calendar-alt"></i>
            Calendario de Mantenimientos
        </div>
        <div class="calendar-legend">
            <div class="legend-item"><span class="legend-dot pendiente"></span> Programado</div>
            <div class="legend-item"><span class="legend-dot en-proceso"></span> En Proceso</div>
            <div class="legend-item"><span class="legend-dot completado"></span> Cerrado</div>
            <div class="legend-item"><span class="legend-dot cancelado"></span> Anulado</div>
        </div>
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">Todos</button>
        <button class="filter-btn" data-filter="PROGRAMADO">Programado</button>
        <button class="filter-btn" data-filter="EN_PROCESO">En Proceso</button>
        <button class="filter-btn" data-filter="CERRADO">Cerrado</button>
        <button class="filter-btn" data-filter="ANULADO">Anulado</button>
    </div>

    <div id="calendar"></div>
</div>

<div class="modal-backdrop-custom" id="modalBackdrop"></div>
<div class="modal-custom" id="mantModal">
    <div class="modal-custom-header">
        <div class="modal-custom-title" id="modalTitle">Detalle de Mantenimiento</div>
        <button class="modal-custom-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-custom-body" id="modalBody">
    </div>
    <div class="modal-custom-footer">
        <a href="#" id="modalLink" class="btn btn-sm btn-primary" style="background:var(--teal);border:none;border-radius:8px;padding:8px 16px;text-decoration:none;font-size:.8rem;font-weight:600;">Ver Completo</a>
    </div>
</div>

<script>
let calendar;
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('<?= e(base_url()) ?>/index.php?route=ajax_mantenimientos_calendario&filter=' + currentFilter)
                .then(res => res.json())
                .then(data => {
                    const events = data.map(m => ({
                        id: m.id,
                        title: m.title,
                        start: m.start,
                        end: m.end,
                        className: 'event-' + m.estado.toLowerCase().replace('_', '-'),
                        extendedProps: m
                    }));
                    successCallback(events);
                })
                .catch(err => failureCallback(err));
        },
        eventClick: function(info) {
            showMantDetail(info.event.extendedProps);
        },
        height: 'auto',
        locale: 'es'
    });
    
    calendar.render();
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            calendar.refetchEvents();
        });
    });
});

function showMantDetail(m) {
    document.getElementById('modalTitle').textContent = 'Mantenimiento #' + m.id;
    document.getElementById('modalLink').href = '<?= e(base_url()) ?>/index.php?route=mantenimiento_ver&id=' + m.id;
    
    const estadoClass = 'status-' + m.estado.toLowerCase().replace('_', '-');
    
    document.getElementById('modalBody').innerHTML = `
        <div class="detail-row">
            <div class="detail-label">Estado</div>
            <div class="detail-value"><span class="detail-value status-badge ${estadoClass}">${m.estado}</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Equipo</div>
            <div class="detail-value">${m.equipo_nombre || 'Sin equipo'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Tipo</div>
            <div class="detail-value">${m.tipo || 'Preventivo'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Prioridad</div>
            <div class="detail-value">${m.prioridad || 'Media'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Programado</div>
            <div class="detail-value">${m.fecha_programada || 'Sin fecha'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Inicio</div>
            <div class="detail-value">${m.fecha_inicio || 'Sin iniciar'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Fin</div>
            <div class="detail-value">${m.fecha_fin || 'Sin finalizar'}</div>
        </div>
        ${m.falla_reportada ? `
        <div class="detail-row">
            <div class="detail-label">Falla</div>
            <div class="detail-value">${m.falla_reportada}</div>
        </div>
        ` : ''}
    `;
    
    document.getElementById('modalBackdrop').classList.add('show');
    document.getElementById('mantModal').classList.add('show');
}

function closeModal() {
    document.getElementById('modalBackdrop').classList.remove('show');
    document.getElementById('mantModal').classList.remove('show');
}

document.getElementById('modalBackdrop').addEventListener('click', closeModal);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
