@extends('layouts.app')

@section('content')
    <!-- Alerts -->
    @if(session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0; font-size: 1.5rem; font-weight: 600;">Planilla de Turnos - {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</h1>
        
        <!-- Color Legend -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; background: white; padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <div class="legend-item"><div class="legend-color shift-partido"></div><span>Partido</span></div>
            <div class="legend-item"><div class="legend-color area-general"></div><span>General</span></div>
            <div class="legend-item"><div class="legend-color area-electro"></div><span>Electro</span></div>
            <div class="legend-item"><div class="legend-color shift-morning"></div><span>Mañana (<10am)</span></div>
            <div class="legend-item"><div class="legend-color shift-afternoon"></div><span>Tarde (>=10am)</span></div>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <!-- Auto-Generate Day Form -->
            <form action="{{ route('shifts.generate_day') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($month)->copy()->startOfMonth()->format('Y-m-d') }}">
                <button type="submit" style="padding: 0.5rem 1rem; background-color: #10B981; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    Generar 1 Día
                </button>
            </form>

            <form action="{{ route('shifts.generate_range') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($month)->copy()->startOfMonth()->format('Y-m-d') }}">
                <input type="hidden" name="days" value="14">
                <button type="submit" style="padding: 0.5rem 1rem; background-color: #3B82F6; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    Generar 2 Semanas
                </button>
            </form>
            
            <button style="padding: 0.5rem 1rem; background-color: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">Guardar Cambios</button>
        </div>
    </div>

    <div class="layout-wrapper">
        <div class="table-section">
            <div class="table-container">
                <table class="excel-table">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                @php 
                                    $currentDateObj = \Carbon\Carbon::parse($month)->setDay($i);
                                    $isToday = $currentDateObj->isToday();
                                @endphp
                                <th class="day-header {{ $isToday ? 'current-day-header' : '' }}" 
                                    {!! $isToday ? 'id="current-day-col"' : '' !!}
                                    onclick="selectDay({{ $i }}, '{{ $currentDateObj->translatedFormat('l d') }}')"
                                    style="cursor: pointer;">
                                    <span style="text-transform: capitalize;">{{ $currentDateObj->translatedFormat('D') }}</span> {{ $i }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td style="cursor: pointer;" onclick="openAbsenceModal({{ $employee->id }}, '{{ $employee->name }}')" title="Registrar Novedad">
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 600; color: var(--primary);">{{ $employee->name }}</span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $employee->areas->first()->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                    @php 
                                        $carbonDate = \Carbon\Carbon::parse($month)->setDay($i);
                                        $currentDate = $carbonDate->format('Y-m-d');
                                        $isTodayCell = $carbonDate->isToday();
                                        
                                        // Ver si hay ausencia
                                        $isAbsent = false;
                                        $absenceType = '';
                                        if(isset($absences[$employee->id])) {
                                            foreach($absences[$employee->id] as $absence) {
                                                if($currentDate >= $absence->start_date && $currentDate <= $absence->end_date) {
                                                    $isAbsent = true;
                                                    $absenceType = strtoupper(substr($absence->type, 0, 3)); // VAC, INC, CAL
                                                    break;
                                                }
                                            }
                                        }

                                        // Ver si hay turno
                                        $cellContent = '--';
                                        $cellStyle = '';
                                        $columnClass = $isTodayCell ? 'current-day-column' : '';
                                        $shiftType = '';
                                        $areaNameData = '';
                                        
                                        if($isAbsent) {
                                            $cellContent = $absenceType;
                                            $cellStyle = 'background-color: #FEE2E2; color: #DC2626; font-weight: bold; font-size: 0.8rem;';
                                            $shiftType = 'ausencia';
                                        } else if (isset($shifts[$employee->id])) {
                                            $shift = $shifts[$employee->id]->firstWhere('date', $currentDate);
                                            if ($shift) {
                                                $cellContent = str_replace('|', '<br>', $shift->schedule);
                                                $shiftType = $shift->type;
                                                $areaNameData = strtolower($shift->area->name ?? '');

                                                // Priority for colors
                                                if($shift->type == 'partido') {
                                                    $columnClass .= ' shift-partido';
                                                } else {
                                                    // Identify area
                                                    if (str_contains($areaNameData, 'electro')) {
                                                        $columnClass .= ' area-electro';
                                                    } elseif (str_contains($areaNameData, 'general')) {
                                                        $columnClass .= ' area-general';
                                                    } else {
                                                        // Fallback to time-based
                                                        $startTime = explode('-', $shift->schedule)[0];
                                                        $hour = (int)explode(':', $startTime)[0];
                                                        if ($hour < 10) {
                                                            $columnClass .= ' shift-morning';
                                                        } else {
                                                            $columnClass .= ' shift-afternoon';
                                                        }
                                                    }
                                                }
                                                
                                                $cellStyle = 'font-size: 0.75rem; line-height: 1.2;';
                                            }
                                        }
                                    @endphp
                                    
                                    <td class="editable-cell {{ $columnClass }} day-col-{{$i}}" 
                                        data-day="{{$i}}"
                                        data-type="{{$shiftType}}"
                                        data-absence="{{$absenceType}}"
                                        data-area="{{$areaNameData}}"
                                        style="{{ $cellStyle }}">
                                        @if($shiftType != '')
                                            <span class="cell-options-trigger" 
                                                  onclick="event.stopPropagation(); alert('Gestionar turno: {{ $employee->name }} - Día {{ $i }}')"
                                                  title="Opciones">⋮</span>
                                        @endif
                                        {!! $cellContent !!}
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Panel -->
        <aside class="summary-panel">
            <div id="summary-empty" style="text-align: center; color: var(--text-muted); margin-top: 2rem;">
                <p>Selecciona un día para ver el resumen</p>
            </div>
            
            <div id="summary-content" style="display: none;">
                <div class="summary-title" id="summary-day-name">Día</div>
                
                <div class="stat-group">
                    <span class="stat-label">Resumen General</span>
                    <div class="stat-row">
                        <span>Total Empleados</span>
                        <span class="stat-value">{{ count($employees) }}</span>
                    </div>
                </div>

                <div class="stat-group">
                    <span class="stat-label">Estado del Personal</span>
                    <div class="stat-row" id="row-working">
                        <span>Trabajando</span>
                        <span class="stat-value" id="stat-total">0</span>
                    </div>
                    <div class="stat-row" id="row-vac" style="display: none;">
                        <span>Vacaciones</span>
                        <span class="stat-value" id="stat-vac">0</span>
                    </div>
                    <div class="stat-row" id="row-inc" style="display: none;">
                        <span>Incapacidad</span>
                        <span class="stat-value" id="stat-inc">0</span>
                    </div>
                    <div class="stat-row" id="row-cal" style="display: none;">
                        <span>Calamidad</span>
                        <span class="stat-value" id="stat-cal">0</span>
                    </div>
                    <div class="stat-row" id="row-libre">
                        <span>Libres</span>
                        <span class="stat-value" id="stat-libre">0</span>
                    </div>
                </div>

                <div class="stat-group">
                    <span class="stat-label">Por Tipo de Turno</span>
                    <div class="stat-row">
                        <span>Normales</span>
                        <span class="stat-value" id="stat-normal">0</span>
                    </div>
                    <div class="stat-row">
                        <span>Partidos</span>
                        <span class="stat-value" id="stat-partido">0</span>
                    </div>
                </div>

                <div class="stat-group">
                    <span class="stat-label">Desglose por Áreas</span>
                    <div id="area-stats-container">
                        <!-- Dynamic area rows will be injected here -->
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Absence Modal -->
    <div id="absenceModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; justify-content: center; align-items: center;">
        <div style="background: white; padding: 2rem; border-radius: 8px; width: 400px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <h2 style="margin-top: 0;">Registrar Novedad</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Empleado: <strong id="modalEmployeeName"></strong></p>
            
            <form action="{{ route('absences.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" id="modalEmployeeId">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Tipo de Novedad</label>
                    <select name="type" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                        <option value="vacation">Vacaciones</option>
                        <option value="incapacity">Incapacidad</option>
                        <option value="calamity">Calamidad</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Fecha de Inicio</label>
                    <input type="date" name="start_date" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Fecha Final</label>
                    <input type="date" name="end_date" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="closeAbsenceModal()" style="padding: 0.5rem 1rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 4px; cursor: pointer;">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.table-container');
            const currentDayCol = document.getElementById('current-day-col');

            // 1. Auto-center current day
            if (currentDayCol && container) {
                const containerWidth = container.offsetWidth;
                const colOffsetLeft = currentDayCol.offsetLeft;
                const colWidth = currentDayCol.offsetWidth;
                
                // Adjust scroll so the column is at the center
                container.scrollLeft = colOffsetLeft - (containerWidth / 2) + (colWidth / 2);
            }

            // 2. Drag-to-scroll implementation
            let isDown = false;
            let startX;
            let scrollLeft;

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.classList.add('active');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.classList.remove('active');
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                container.classList.remove('active');
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Multiply speed
                container.scrollLeft = scrollLeft - walk;
            });
            
            // 3. Selection of today by default
            const todayNum = new Date().getDate();
            // find the header with that day and call select (not perfect if month changes but works for demo)
            const headers = document.querySelectorAll('.day-header');
            headers.forEach(h => {
                if(h.innerText.includes(todayNum.toString())) {
                    h.click();
                }
            });
        });

        // Loading Logic
        document.querySelectorAll('form').forEach(form => {
            if (form.action.includes('generate')) {
                form.addEventListener('submit', function() {
                    document.getElementById('loading-overlay').style.display = 'flex';
                });
            }
        });

        function selectDay(dayNum, dayLabel) {
            // Update UI
            document.getElementById('summary-empty').style.display = 'none';
            document.getElementById('summary-content').style.display = 'block';
            document.getElementById('summary-day-name').innerText = dayLabel;
            
            // Reset highlights
            document.querySelectorAll('.day-header').forEach(h => {
                h.classList.remove('selected-day-header');
            });
            document.querySelectorAll('.editable-cell').forEach(c => {
                c.classList.remove('selected-day-column-highlight');
            });
            
            // Find and highlight current header
            const header = document.querySelector(`.day-header:nth-child(${dayNum + 1})`);
            if (header) header.classList.add('selected-day-header');
            
            // Add highlights to selected column cells
            const cells = document.querySelectorAll(`.day-col-${dayNum}`);
            cells.forEach(c => c.classList.add('selected-day-column-highlight'));
            
            // Calculate Stats
            let totalEmployees = {{ count($employees) }};
            let working = 0;
            let normal = 0;
            let partido = 0;
            let vac = 0;
            let inc = 0;
            let cal = 0;
            let areaCounts = {};
            
            cells.forEach(c => {
                const type = c.getAttribute('data-type');
                const area = c.getAttribute('data-area');
                const absence = c.getAttribute('data-absence');
                
                if (type === 'normal' || type === 'partido') {
                    working++;
                    if (type === 'normal') normal++;
                    if (type === 'partido') partido++;
                    
                    if (area) {
                        const areaName = area.trim().toLowerCase();
                        if (areaName) {
                            areaCounts[areaName] = (areaCounts[areaName] || 0) + 1;
                        }
                    }
                } else if (type === 'ausencia') {
                    if (absence === 'VAC') vac++;
                    if (absence === 'INC') inc++;
                    if (absence === 'CAL') cal++;
                }
            });
            
            let libres = totalEmployees - working - (vac + inc + cal);
            
            // Update panel
            document.getElementById('stat-total').innerText = working;
            document.getElementById('stat-normal').innerText = normal;
            document.getElementById('stat-partido').innerText = partido;
            document.getElementById('stat-vac').innerText = vac;
            document.getElementById('stat-inc').innerText = inc;
            document.getElementById('stat-cal').innerText = cal;
            document.getElementById('stat-libre').innerText = libres;

            // Show/Hide rows based on count
            document.getElementById('row-vac').style.display = vac > 0 ? 'flex' : 'none';
            document.getElementById('row-inc').style.display = inc > 0 ? 'flex' : 'none';
            document.getElementById('row-cal').style.display = cal > 0 ? 'flex' : 'none';
            
            // Update Dynamic Areas
            const areaContainer = document.getElementById('area-stats-container');
            areaContainer.innerHTML = '';
            
            // Sort areas by count descending
            const sortedAreas = Object.keys(areaCounts).sort((a, b) => areaCounts[b] - areaCounts[a]);
            
            if (sortedAreas.length === 0) {
                areaContainer.innerHTML = '<div style="font-size: 0.8rem; color: var(--text-muted); text-align: center;">Sin áreas asignadas</div>';
            } else {
                sortedAreas.forEach(areaName => {
                    const row = document.createElement('div');
                    row.className = 'stat-row';
                    
                    // Capitalize area name
                    const displayLabel = areaName.charAt(0).toUpperCase() + areaName.slice(1);
                    
                    row.innerHTML = `
                        <span>${displayLabel}</span>
                        <span class="stat-value">${areaCounts[areaName]}</span>
                    `;
                    areaContainer.appendChild(row);
                });
            }
        }

        function openAbsenceModal(employeeId, employeeName) {
            document.getElementById('modalEmployeeId').value = employeeId;
            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('absenceModal').style.display = 'flex';
        }

        function closeAbsenceModal() {
            document.getElementById('absenceModal').style.display = 'none';
        }
    </script>
@endsection
