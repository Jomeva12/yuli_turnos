@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: #1e293b;">Asignación Manual de Turnos</h1>
                <p style="color: #64748b; margin-top: 0.25rem;">Personalizando el horario de <strong>{{ $employee->name }}</strong> para {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('shifts.index', ['month' => $month]) }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i> Volver a Planilla
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar: Templates -->
        <div class="col-12 col-xl-3">
            <div class="card-templates sticky-top" style="top: 1rem; max-height: calc(100vh - 2rem); overflow-y: auto; z-index: 100;">
                <h5 class="mb-4 text-center font-weight-bold" style="color: #1e293b;">PLANTILLAS</h5>
                
                @foreach(['mañana', 'tarde', 'partido', 'otros'] as $type)
                    <div class="template-group mb-4 pb-3" style="border-right: none; border-bottom: 1px solid #f1f5f9;">
                        <h6 class="group-title">
                            @if($type == 'mañana') Mañana
                            @elseif($type == 'tarde') Tarde
                            @elseif($type == 'partido') Partido
                            @else Otros / Sistema
                            @endif
                        </h6>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            @foreach($groupedTemplates[$type] ?? [] as $t)
                                <div class="drag-box" 
                                     draggable="true" 
                                     ondragstart="drag(event)"
                                     data-schedule="{{ $t->schedule }}" 
                                     data-type="{{ $t->type }}">
                                    {{ $t->schedule }}
                                </div>
                            @endforeach
                            
                            @if($type == 'otros')
                                <div class="drag-box" 
                                     draggable="true" 
                                     ondragstart="drag(event)"
                                     data-schedule="DESCANSO" 
                                     data-type="descanso"
                                     style="border-color: #64748b; color: #64748b; width: 100%;">
                                    DESCANSO
                                </div>
                                <div class="drag-box" 
                                     draggable="true" 
                                     ondragstart="drag(event)"
                                     data-schedule="NONE" 
                                     data-type="none"
                                     style="border-color: #ef4444; color: #ef4444; width: 100%;">
                                    LIMPIAR DÍA
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content: Timeline -->
        <div class="col-12 col-xl-9">
            <div class="card-timeline">
                <div class="table-responsive">
                    <table class="timeline-table">
                        <thead>
                            <tr>
                                <th class="sticky-col col-employee">ESTADO</th>
                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                    @php 
                                        $carbonDate = \Carbon\Carbon::parse($month)->setDay($i);
                                        $isSunday = $carbonDate->dayOfWeek == 0;
                                    @endphp
                                    <th class="day-head {{ $isSunday ? 'is-sunday' : '' }}">
                                        <span class="d-block">{{ $carbonDate->translatedFormat('D.') }} {{ $i }}</span>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sticky-col employee-info">
                                    <div class="emp-name">{{ $employee->name }}</div>
                                    <div class="emp-dots">
                                        @foreach($employee->areas as $area)
                                            <span class="dot" style="background: #4f46e5;"></span>
                                        @endforeach
                                    </div>
                                </td>
                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                    @php 
                                        $currentDateStr = \Carbon\Carbon::parse($month)->setDay($i)->format('Y-m-d');
                                        $absence = $absences->first(fn($a) => $currentDateStr >= $a->start_date && $currentDateStr <= $a->end_date);
                                        $shift = $shifts->get($i);
                                    @endphp
                                    <td class="drop-zone" 
                                        id="cell-{{ $currentDateStr }}"
                                        ondragover="allowDrop(event)"
                                        ondragenter="handleDragEnter(this)"
                                        ondragleave="handleDragLeave(this)"
                                        ondrop="drop(event, '{{ $currentDateStr }}')">
                                        
                                        <div class="cell-status" id="status-{{ $currentDateStr }}"></div>
        
                                        @if ($absence)
                                            <div class="absence-badge">{{ strtoupper(substr($absence->type, 0, 3)) }}</div>
                                        @elseif ($shift)
                                            <div class="shift-content {{ $shift->type == 'partido' ? 'shift-partido' : ($shift->type == 'descanso' ? 'shift-descanso' : '') }}">
                                                {!! str_replace('|', '<br>', $shift->schedule) !!}
                                            </div>
                                        @else
                                            <span class="empty-mark">--</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Global refinement */
    body { background: #f8fafc; }
    .btn-back {
        padding: 0.5rem 1.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    .btn-back:hover { background: #f1f5f9; color: #1e293b; }

    /* Templates Area */
    .card-templates {
        background: white;
        margin-bottom: 2rem;
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .template-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
    .template-group { 
        border-right: 1px solid #f1f5f9;
        padding: 0 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .template-group:last-child { border-right: none; }
    
    .group-title {
        text-align: center;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1.5rem;
        color: #64748b;
        width: 100%;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.5rem;
    }
    .drag-box {
        width: 140px;
        padding: 0.75rem 0.5rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.75rem;
        border: 2px solid #1e293b;
        background: white;
        cursor: grab;
        transition: all 0.2s;
        border-radius: 4px;
        margin-bottom: 0.75rem;
    }
    .drag-box:active { cursor: grabbing; }
    .drag-box:hover { transform: scale(1.02); background: #f8fafc; }

    /* Timeline Table */
    .card-timeline {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .timeline-table { width: 100%; border-collapse: collapse; }
    .timeline-table th, .timeline-table td {
        border: 1px solid #e2e8f0;
        padding: 0.75rem 0.5rem;
        text-align: center;
        min-width: 85px;
    }
    
    .sticky-col {
        position: sticky;
        left: 0;
        background: white;
        z-index: 10;
        min-width: 180px !important;
        border-right: 2px solid #e2e8f0 !important;
    }
    
    .col-employee { background: #fdfdfd; font-size: 0.8rem; font-weight: 700; color: #64748b; }
    .employee-info { text-align: left !important; padding-left: 1.5rem !important; }
    .emp-name { font-weight: 700; color: #0f172a; font-size: 0.85rem; text-transform: uppercase; }
    .emp-dots { display: flex; gap: 4px; margin-top: 4px; }
    .dot { width: 8px; height: 8px; border-radius: 2px; display: inline-block; }

    .day-head {
        background: #f8fafc;
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        padding: 10px 5px !important;
    }
    .is-sunday { background: #fecaca !important; color: #7f1d1d !important; }

    .drop-zone { height: 75px; position: relative; transition: background 0.2s; }
    .drop-zone.drag-over { background: #eff6ff !important; border: 2px dashed #3b82f6 !important; }
    
    .empty-mark { color: #94a3b8; font-weight: 500; }
    .absence-badge { font-weight: 700; color: #0ea5e9; font-size: 0.8rem; }
    
    .shift-content { font-size: 0.75rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .shift-partido { color: #b45309; }
    .shift-descanso { color: #64748b; font-style: italic; }

    .cell-status { position: absolute; top: 4px; right: 4px; }

    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .spinning { animation: spin 1s linear infinite; }
</style>

<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function handleDragEnter(el) {
    el.classList.add('drag-over');
}

function handleDragLeave(el) {
    el.classList.remove('drag-over');
}

function drag(ev) {
    ev.dataTransfer.setData("schedule", ev.target.dataset.schedule);
    ev.dataTransfer.setData("type", ev.target.dataset.type);
    ev.dataTransfer.effectAllowed = "copy";
}

function drop(ev, date) {
    ev.preventDefault();
    const cell = ev.currentTarget;
    cell.classList.remove('drag-over');
    
    const schedule = ev.dataTransfer.getData("schedule");
    const type = ev.dataTransfer.getData("type");
    
    if (cell.querySelector('.absence-badge')) {
        if (!confirm('Este día tiene una novedad registrada. ¿Deseas sobreescribirla?')) return;
    }

    const statusEl = document.getElementById('status-' + date);
    statusEl.innerHTML = '<i class="fas fa-circle-notch spinning text-primary" style="font-size: 0.6rem;"></i>';

    fetch('{{ route("api.shifts.manual_assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            employee_id: {{ $employee->id }},
            date: date,
            schedule: schedule,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCellUI(date, data.shift, schedule, type);
            statusEl.innerHTML = '<i class="fas fa-check text-success" style="font-size: 0.6rem;"></i>';
            setTimeout(() => statusEl.innerHTML = '', 1500);
        } else {
            alert('Error: ' + (data.message || 'No se pudo guardar'));
            statusEl.innerHTML = '<i class="fas fa-times text-danger"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusEl.innerHTML = '<i class="fas fa-wifi text-danger"></i>';
    });
}

function updateCellUI(date, shift, originalSchedule, originalType) {
    const cell = document.getElementById('cell-' + date);
    
    if (!shift || originalSchedule === 'NONE') {
        cell.innerHTML = `<div class="cell-status" id="status-${date}"></div><span class="empty-mark">--</span>`;
        return;
    }

    let typeClass = '';
    if (originalType === 'partido') typeClass = 'shift-partido';
    if (originalType === 'descanso') typeClass = 'shift-descanso';

    const formattedSchedule = originalSchedule.replace('|', '<br>');
    cell.innerHTML = `
        <div class="cell-status" id="status-${date}"></div>
        <div class="shift-content ${typeClass}">${formattedSchedule}</div>
    `;
}
</script>
@endsection
