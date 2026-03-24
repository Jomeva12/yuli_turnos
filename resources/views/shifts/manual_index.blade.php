@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #2563eb;
        --primary-light: #dbeafe;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --bg-main: #f8fafc;
    }

    #manual-assignment-container {
        padding: 1.5rem;
    }

    /* Side-by-side Layout */
    .assignment-layout {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        flex-direction: row; /* Default */
    }

    /* Template Sidebar */
    .template-sidebar {
        width: 450px; /* Increased to fit 3 vertical columns */
        flex-shrink: 0;
        order: 2; /* Move to the right */
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem; /* Reduced padding */
        height: calc(100vh - 120px);
        position: sticky;
        top: 80px;
        overflow-y: auto;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .template-columns {
        display: flex;
        gap: 0.5rem;
        height: 100%;
        align-items: flex-start;
    }

    .template-col {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    /* Timeline Main Area */
    .timeline-container {
        flex-grow: 1;
        min-width: 0; /* Crucial for flex child overflow */
        order: 1; /* Stay on the left */
        background: white;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        /* Remove padding to avoid sticky element offset issues */
        padding: 0;
        overflow: auto; /* Enable both X and Y scroll */
        height: calc(100vh - 200px); /* Fixed height for vertical scroll */
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        scrollbar-width: thin;
        scrollbar-color: var(--primary) transparent;
        position: relative;
        cursor: grab;
    }

    .timeline-container.grabbing {
        cursor: grabbing;
        user-select: none;
    }

    /* Custom Scrollbar for Chrome/Safari/Edge */
    .timeline-container::-webkit-scrollbar {
        height: 8px;
    }
    .timeline-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .timeline-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .timeline-container::-webkit-scrollbar-thumb:hover {
        background: var(--primary);
    }

    .group-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        padding-bottom: 0.25rem;
        border-bottom: 2px solid var(--primary-light);
    }

    .drag-box {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 2px; /* Minimal padding */
        margin-bottom: 0; 
        cursor: grab;
        font-size: 0.62rem; /* Even smaller font */
        font-weight: 600;
        text-align: center;
        transition: all 0.1s;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%; /* Full width of the sub-column */
        min-width: 0;
    }

    .drag-box:hover {
        border-color: var(--primary);
        background: var(--primary-light);
        color: var(--primary);
    }

    .drag-box:active {
        cursor: grabbing;
    }

    /* Timeline Table Styles */
    .timeline-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }

    .timeline-table th, .timeline-table td {
        padding: 0;
        border-right: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        min-width: 70px;
        height: 60px;
        text-align: center;
        vertical-align: middle;
        /* Removed position: relative to avoid breaking sticky context */
    }

    .sticky-col {
        position: sticky !important;
        left: 0 !important;
        z-index: 100 !important;
        background-color: white !important;
        border-right: 2px solid var(--border-color) !important;
        min-width: 200px !important;
        width: 200px !important;
        text-align: left !important;
        padding: 0.5rem 1rem !important;
        box-shadow: 4px 0 8px -2px rgba(0,0,0,0.1);
    }

    /* Ensure specific headers stay sticky on top too if scrolled down */
    .day-head.sticky-col {
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 200 !important;
        background-color: #f8fafc !important;
    }

    .day-head {
        background: #f8fafc !important;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.75rem;
        height: 40px !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 50 !important;
    }

    .is-sunday { background: #fee2e2 !important; }

    .drop-zone {
        transition: all 0.2s;
    }

    .drop-zone.drag-enter {
        background-color: var(--primary-light) !important;
        box-shadow: inset 0 0 0 2px var(--primary);
    }

    .shift-content {
        font-size: 0.7rem;
        font-weight: 700;
        line-height: 1.1;
        color: #1e40af;
    }

    .shift-partido { color: #991b1b; }
    .shift-descanso { color: #64748b; font-style: italic; }

    .absence-badge {
        font-size: 0.65rem;
        font-weight: 900;
        background: #fef3c7;
        color: #d97706;
        padding: 2px 4px;
        border-radius: 4px;
    }

    .cell-status {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-saving { background: #3b82f6; animation: spin 1s linear infinite; }
    .status-success { background: #10b981; }
    .status-error { background: #ef4444; }

    @keyframes spin {
        0% { opacity: 0.2; }
        50% { opacity: 1; }
        100% { opacity: 0.2; }
    }

    .emp-name {
        font-weight: 700;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .emp-areas {
        font-size: 0.65rem;
        color: var(--text-muted);
    }

    /* Legend */
    .legend-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 0.35rem 1rem;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        font-size: 0.7rem;
        color: var(--text-muted);
        align-items: center;
        width: fit-content;
    }
</style>

<div id="manual-assignment-container">
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-main);">Asignación Manual Global</h1>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">
                Arrastra turnos desde la izquierda hacia cualquier celda del calendario.
            </p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="month" id="global-month-selector" value="{{ $month }}" 
                   style="padding: 0.4rem 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-main); font-weight: 500; font-size: 0.9rem; cursor: pointer; outline: none; background: white;">
            
            <a href="{{ route('shifts.index', ['month' => $month]) }}" 
               style="padding: 0.6rem 1.25rem; background: white; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Calendario
            </a>
        </div>
    </div>

    <div class="legend-bar">
        <div style="font-weight: 700; color: #64748b; margin-right: 0.25rem;">LEYENDA:</div>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
            <div style="width: 10px; height: 10px; background: #fef3c7; border-radius: 2px; border: 1px solid #fde68a;"></div>
            <span>Novedad</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
            <div style="width: 10px; height: 10px; background: #fee2e2; border-radius: 2px; border: 1px solid #fecaca;"></div>
            <span>Turno Partido</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
            <div style="width: 10px; height: 10px; background: #f1f5f9; border-radius: 2px; border: 1px solid #e2e8f0;"></div>
            <span>Descanso / Comodín</span>
        </div>
    </div>

    <div class="assignment-layout">
        <!-- Sidebar -->
        <div class="template-sidebar">
            <div class="template-columns">
                <!-- Columna Mañana -->
                <div class="template-col">
                    <h6 class="group-title">Mañana</h6>
                    @foreach($groupedTemplates['mañana'] ?? [] as $t)
                        <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="{{ $t->schedule }}" data-type="{{ $t->type }}" title="{{ $t->schedule }}">
                            {{ $t->schedule }}
                        </div>
                    @endforeach
                </div>

                <!-- Columna Tarde -->
                <div class="template-col">
                    <h6 class="group-title">Tarde</h6>
                    @foreach($groupedTemplates['tarde'] ?? [] as $t)
                        <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="{{ $t->schedule }}" data-type="{{ $t->type }}" title="{{ $t->schedule }}">
                            {{ $t->schedule }}
                        </div>
                    @endforeach
                </div>

                <!-- Columna Partido + Otros -->
                <div class="template-col">
                    <h6 class="group-title">Partido / Otros</h6>
                    @foreach($groupedTemplates['partido'] ?? [] as $t)
                        <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="{{ $t->schedule }}" data-type="{{ $t->type }}" title="{{ $t->schedule }}">
                            {{ $t->schedule }}
                        </div>
                    @endforeach
                    
                    <hr style="margin: 0.5rem 0; border-color: var(--border-color);">
                    
                    @foreach($groupedTemplates['otros'] ?? [] as $t)
                        <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="{{ $t->schedule }}" data-type="{{ $t->type }}" title="{{ $t->schedule }}">
                            {{ $t->schedule }}
                        </div>
                    @endforeach

                    <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="DESCANSO" data-type="descanso" style="border-color: #64748b; color: #64748b;">
                        DESCANSO
                    </div>
                    <div class="drag-box" draggable="true" ondragstart="drag(event)" data-schedule="NONE" data-type="none" style="border-color: #ef4444; color: #ef4444;">
                        LIMPIAR
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Area -->
        <div class="timeline-container">
            <table class="timeline-table">
                    <thead>
                        <tr>
                            <th class="sticky-col day-head">ASESOR</th>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php 
                                    $carbonDate = \Carbon\Carbon::parse($month)->setDay($i);
                                    $isSunday = $carbonDate->dayOfWeek == 0;
                                @endphp
                                <th class="day-head {{ $isSunday ? 'is-sunday' : '' }}">
                                    {{ $carbonDate->locale('es')->translatedFormat('D') }}<br>{{ $i }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td class="sticky-col">
                                <div class="emp-name">{{ $employee->name }}</div>
                                <div class="emp-areas">
                                    {{ $employee->areas->pluck('name')->implode(', ') }}
                                </div>
                            </td>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php 
                                    $dateStr = \Carbon\Carbon::parse($month)->setDay($i)->format('Y-m-d');
                                    $empShifts = $shifts->get($employee->id, collect());
                                    $shift = $empShifts->first(fn($s) => $s->date == $dateStr);
                                    $empAbsences = $absences->get($employee->id, collect());
                                    $absence = $empAbsences->first(fn($a) => $dateStr >= $a->start_date && $dateStr <= $a->end_date);
                                @endphp
                                <td class="drop-zone" 
                                    id="cell-{{ $employee->id }}-{{ $dateStr }}"
                                    ondragover="allowDrop(event)"
                                    ondragenter="handleDragEnter(this)"
                                    ondragleave="handleDragLeave(this)"
                                    ondrop="drop(event, {{ $employee->id }}, '{{ $dateStr }}')">
                                    
                                    <div class="cell-status" id="status-{{ $employee->id }}-{{ $dateStr }}"></div>
    
                                    @if ($absence)
                                        <div class="absence-badge">{{ strtoupper(substr($absence->type, 0, 3)) }}</div>
                                    @elseif ($shift)
                                        <div class="shift-content {{ $shift->type == 'partido' ? 'shift-partido' : ($shift->type == 'descanso' ? 'shift-descanso' : '') }}">
                                            {!! str_replace('|', '<br>', $shift->schedule) !!}
                                        </div>
                                    @else
                                        <span style="color: #cbd5e1; font-size: 0.6rem;">--</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.timeline-container');
        
        // Drag-to-scroll implementation
        let isDown = false;
        let startX;
        let scrollLeft;

        container.addEventListener('mousedown', (e) => {
            // Only scroll if not clicking on a draggable element or drop-zone content
            if (e.target.closest('.drag-box')) return;
            
            isDown = true;
            container.classList.add('grabbing');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('grabbing');
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('grabbing');
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed
            container.scrollLeft = scrollLeft - walk;
        });
    });

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        const schedule = ev.target.getAttribute('data-schedule');
        const type = ev.target.getAttribute('data-type');
        ev.dataTransfer.setData("schedule", schedule);
        ev.dataTransfer.setData("type", type);
    }

    function handleDragEnter(el) { el.classList.add('drag-enter'); }
    function handleDragLeave(el) { el.classList.remove('drag-enter'); }

    function drop(ev, employeeId, date) {
        ev.preventDefault();
        const dropZone = ev.currentTarget;
        dropZone.classList.remove('drag-enter');

        const schedule = ev.dataTransfer.getData("schedule");
        const type = ev.dataTransfer.getData("type");

        if (!schedule) return;

        updateCellUI(employeeId, date, schedule, type);
        saveShift(employeeId, date, schedule, type);
    }

    function updateCellUI(employeeId, date, schedule, type) {
        const cell = document.getElementById(`cell-${employeeId}-${date}`);
        const status = document.getElementById(`status-${employeeId}-${date}`);
        
        status.className = 'cell-status status-saving';

        if (schedule === 'NONE') {
            cell.innerHTML = '<div class="cell-status" id="status-' + employeeId + '-' + date + '"></div><span style="color: #cbd5e1; font-size: 0.6rem;">--</span>';
        } else {
            let html = '<div class="cell-status" id="status-' + employeeId + '-' + date + '"></div>';
            let cssClass = 'shift-content';
            if (type === 'partido') cssClass += ' shift-partido';
            if (type === 'descanso') cssClass += ' shift-descanso';
            
            html += `<div class="${cssClass}">${schedule.replace('|', '<br>')}</div>`;
            cell.innerHTML = html;
        }
    }

    function saveShift(employeeId, date, schedule, type) {
        fetch('{{ route("api.shifts.manual_assign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                date: date,
                schedule: schedule,
                type: type
            })
        })
        .then(response => response.json())
        .then(data => {
            const status = document.getElementById(`status-${employeeId}-${date}`);
            if (data.success) {
                status.className = 'cell-status status-success';
                setTimeout(() => { if(status) status.className = 'cell-status'; }, 2000);
            } else {
                status.className = 'cell-status status-error';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const status = document.getElementById(`status-${employeeId}-${date}`);
            if(status) status.className = 'cell-status status-error';
        });
    }

    document.getElementById('global-month-selector').addEventListener('change', function() {
        window.location.href = `{{ route('shifts.manual_index') }}?month=${this.value}`;
    });
</script>
@endsection
