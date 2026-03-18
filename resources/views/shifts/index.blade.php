@extends('layouts.app')

@section('content')
@php
    $skillColors = [
        ['bg' => '#eef2ff', 'text' => '#4f46e5', 'border' => '#818cf8', 'dot' => '#4f46e5'], // Indigo
        ['bg' => '#ecfdf5', 'text' => '#059669', 'border' => '#34d399', 'dot' => '#059669'], // Emerald
        ['bg' => '#fff7ed', 'text' => '#d97706', 'border' => '#fbbf24', 'dot' => '#d97706'], // Amber
        ['bg' => '#fdf2f8', 'text' => '#db2777', 'border' => '#f472b6', 'dot' => '#db2777'], // Pink
        ['bg' => '#f0f9ff', 'text' => '#0284c7', 'border' => '#38bdf8', 'dot' => '#0284c7'], // Sky
        ['bg' => '#faf5ff', 'text' => '#9333ea', 'border' => '#c084fc', 'dot' => '#9333ea'], // Purple
        ['bg' => '#fff1f2', 'text' => '#e11d48', 'border' => '#fb7185', 'dot' => '#e11d48'], // Rose
        ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#4ade80', 'dot' => '#16a34a'], // Green
        ['bg' => '#fefce8', 'text' => '#ca8a04', 'border' => '#facc15', 'dot' => '#ca8a04'], // Yellow
        ['bg' => '#f5f3ff', 'text' => '#7c3aed', 'border' => '#a78bfa', 'dot' => '#7c3aed'], // Violet
    ];
    $areaToColor = [];
    foreach($areas as $index => $area) {
        $areaToColor[$area->id] = $skillColors[$index % count($skillColors)];
    }
@endphp
<div id="content-area">
    <!-- Alerts -->
    @if(session('success'))
        <div style="background-color: #ecfdf5; color: #065f46; padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #10b981; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--text-main);">Planilla de Turnos</h1>
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-top: 0.5rem;">
                <p style="color: var(--text-muted); margin: 0; font-size: 0.95rem; text-transform: capitalize; font-weight: 500;">
                    {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                </p>
                <!-- Compact Legend -->
                <div style="display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-muted); align-items: center; background: #f8fafc; padding: 0.25rem 0.75rem; border-radius: 20px; border: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.4rem;"><div style="width: 8px; height: 8px; border-radius: 50%; background: #fee2e2; border: 1px solid #fecaca;"></div><span>Partido</span></div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;"><div style="width: 8px; height: 8px; border-radius: 50%; background: #dcfce7; border: 1px solid #bbf7d0;"></div><span>General</span></div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;"><div style="width: 8px; height: 8px; border-radius: 50%; background: #dbeafe; border: 1px solid #bfdbfe;"></div><span>Electro</span></div>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <!-- Auto-Generate Day Form -->
            <form action="{{ route('shifts.generate_day') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($month)->copy()->startOfMonth()->format('Y-m-d') }}">
                <button type="submit" style="padding: 0.6rem 1.25rem; background: white; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    Generar 1 Día
                </button>
            </form>

            <form action="{{ route('shifts.generate_range') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($month)->copy()->startOfMonth()->format('Y-m-d') }}">
                <input type="hidden" name="days" value="14">
                <button type="submit" style="padding: 0.6rem 1.25rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                    Generar 2 Semanas
                </button>
            </form>
            
            <button style="padding: 0.6rem 1.25rem; background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">
                Guardar Cambios
            </button>
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
                                    onclick="selectDay(this, {{ $i }}, '{{ $currentDateObj->translatedFormat('l d') }}')"
                                    data-weekday="{{ $currentDateObj->dayOfWeek }}"
                                    style="cursor: pointer;">
                                    <span style="text-transform: capitalize;">{{ $currentDateObj->translatedFormat('D') }}</span> {{ $i }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="employee-cell" 
                                    onclick="openEmployeeSidebar({{ $employee->id }}, '{{ addslashes($employee->name) }}', this.querySelector('.emp-name-link'))"
                                    style="text-align: center; cursor: pointer; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f1f5f9'"
                                    onmouseout="this.style.backgroundColor='white'"
                                >
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 45px; position: relative;">
                                        <span 
                                            class="emp-name-link"
                                            title="Ver resumen del mes"
                                            style="font-weight: 600; color: #003049; text-align: center;">{{ $employee->name }}</span>
                                        <div style="display: flex; gap: 4px; margin-top: 5px; flex-wrap: wrap; justify-content: center;">
                                            @foreach($employee->areas as $area)
                                                @php $c = $areaToColor[$area->id] ?? ['dot' => '#cbd5e1']; @endphp
                                                <div style="width: 10px; height: 10px; border-radius: 2px; background-color: {{ $c['dot'] }}; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                                </div>
                                            @endforeach
                                            @if($employee->areas->isEmpty())
                                                <span style="font-size: 0.65rem; color: var(--text-muted);">N/A</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Skills Popover -->
                                    <div class="skills-popover">
                                        <div class="popover-header">Habilidades de {{ $employee->name }}</div>
                                        @foreach($employee->areas as $area)
                                            @php $c = $areaToColor[$area->id] ?? ['dot' => '#cbd5e1']; @endphp
                                            <div class="popover-skill">
                                                <div class="popover-dot" style="background-color: {{ $c['dot'] }}"></div>
                                                <span>{{ $area->name }}</span>
                                            </div>
                                        @endforeach
                                        @if($employee->areas->isEmpty())
                                            <div style="font-size: 0.8rem; color: var(--text-muted); padding: 5px 0;">Sin habilidades registradas</div>
                                        @endif
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

        <!-- Summary Panel Container -->
        <div class="summary-wrapper">
            <button class="panel-toggle" id="summary-toggle" onclick="toggleSummaryPanel()" title="Ver Resumen">
                <span id="toggle-icon">◀</span>
            </button>
            <aside class="summary-panel" id="main-summary-panel">
            <div id="summary-empty" style="text-align: center; color: var(--text-muted); margin-top: 2rem; padding: 0 1.5rem;">
                <p>Selecciona un día para ver el resumen</p>
            </div>
            
                <div id="summary-content" style="display: none; flex: 1; overflow-y: auto; padding-right: 2px;">
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
                    <div class="stat-row" id="row-permiso" style="display: none;">
                        <span>Permiso</span>
                        <span class="stat-value" id="stat-permiso">0</span>
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
    </div>

    <!-- Absence Modal -->
    <div id="absenceModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 50; justify-content: center; align-items: center;">
        <div style="background: white; padding: 2rem; border-radius: 8px; width: 400px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <h2 style="margin-top: 0; color: #003049; font-size: 1.5rem;">Registrar Novedad</h2>
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
                    <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Guardar</button>
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

        function toggleSummaryPanel() {
            const panel = document.getElementById('main-summary-panel');
            const icon = document.getElementById('toggle-icon');
            panel.classList.toggle('active');
            
            if (panel.classList.contains('active')) {
                icon.innerText = '▶';
            } else {
                icon.innerText = '◀';
            }
        }

        function selectDay(headerEl, dayNum, dayLabel) {
            // Update UI
            const panel = document.getElementById('main-summary-panel');
            const icon = document.getElementById('toggle-icon');
            
            panel.classList.add('active');
            icon.innerText = '▶';
            
            document.getElementById('summary-empty').style.display = 'none';
            document.getElementById('summary-content').style.display = 'flex';
            document.getElementById('summary-content').style.flexDirection = 'column';
            document.getElementById('summary-day-name').innerText = dayLabel;
            
            // Reset highlights
            document.querySelectorAll('.day-header').forEach(h => {
                h.classList.remove('selected-day-header');
            });
            document.querySelectorAll('.selected-day-column-highlight').forEach(c => {
                c.classList.remove('selected-day-column-highlight');
            });
            
            // Highlight target header
            if (headerEl) headerEl.classList.add('selected-day-header');
            
            // Add highlights to column cells
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
            let permiso = 0;
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
                    if (absence === 'PER') permiso++;
                }
            });
            
            let libres = totalEmployees - working - (vac + inc + cal + permiso);
            
            // Update panel
            document.getElementById('stat-total').innerText = working;
            document.getElementById('stat-normal').innerText = normal;
            document.getElementById('stat-partido').innerText = partido;
            document.getElementById('stat-vac').innerText = vac;
            document.getElementById('stat-inc').innerText = inc;
            document.getElementById('stat-cal').innerText = cal;
            document.getElementById('stat-permiso').innerText = permiso;
            document.getElementById('stat-libre').innerText = libres;

            // Show/Hide rows based on count
            document.getElementById('row-vac').style.display = vac > 0 ? 'flex' : 'none';
            document.getElementById('row-inc').style.display = inc > 0 ? 'flex' : 'none';
            document.getElementById('row-cal').style.display = cal > 0 ? 'flex' : 'none';
            document.getElementById('row-permiso').style.display = permiso > 0 ? 'flex' : 'none';
            
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
    <style>
        .layout-wrapper {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            overflow: hidden;
            height: calc(100vh - 180px);
        }

        .table-section {
            flex: 1;
            min-width: 0; /* Important for flex shrinking */
            height: 100%;
        }

        .table-container {
            height: 100%;
            overflow: auto;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .summary-wrapper {
            position: relative;
            display: flex;
            align-items: flex-start;
            height: 100%;
            overflow: visible;
            z-index: 50;
        }

        .panel-toggle {
            position: absolute;
            left: -26px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 60px;
            background: var(--primary);
            border: none;
            border-radius: 8px 0 0 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: -3px 0 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
            font-size: 0.7rem;
            letter-spacing: -1px;
        }

        .panel-toggle:hover {
            background: var(--primary-hover);
            width: 30px;
            left: -30px;
        }

        .summary-panel {
            width: 0;
            opacity: 0;
            overflow: hidden;
            background: white;
            border: none;
            border-left: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex !important;
            flex-direction: column;
            position: relative;
            box-shadow: -15px 0 30px rgba(0, 0, 0, 0.05);
        }

        .summary-panel.active {
            width: 360px;
            opacity: 1;
            padding: 1.5rem;
            border-left-width: 2px;
            display: flex !important;
            flex-direction: column;
            overflow: hidden; /* Let the inner content div scroll */
        }

        /* When panel is open, the toggle sits at its left edge */
        .summary-panel.active ~ .panel-toggle {
            left: -26px;
            background: var(--primary);
            color: white;
            box-shadow: -3px 0 8px rgba(0,0,0,0.1);
        }

        /* Scrollbar for summary content */
        #summary-content::-webkit-scrollbar { width: 4px; }
        #summary-content::-webkit-scrollbar-track { background: transparent; }
        #summary-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .close-panel {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
            z-index: 10;
        }

        .close-panel:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .summary-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .stat-group {
            margin-bottom: 0.1rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .stat-group:last-child {
            border-bottom: none;
        }

        .stat-label {
            display: block;
            font-size: 0.6rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--primary);
            margin: 0.75rem 0 0.3rem;
            letter-spacing: 0.08em;
            padding-left: 0.5rem;
            border-left: 2px solid var(--primary);
            opacity: 0.65;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.28rem 0.25rem;
            font-size: 0.82rem;
            color: #334155;
            border-radius: 5px;
            transition: background 0.15s;
        }

        .stat-row:hover {
            background: rgba(var(--primary-rgb, 0, 128, 100), 0.05);
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.82rem;
            min-width: 24px;
            text-align: right;
            background: rgba(0,0,0,0.03);
            padding: 1px 7px;
            border-radius: 20px;
        }

        /* Improved Scrollbar for the table */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    </div> <!-- End layout-wrapper -->

    <!-- ===================== EMPLOYEE DETAIL SIDEBAR ===================== -->
    <div id="emp-sidebar-overlay" onclick="closeEmployeeSidebar()" style="position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:1100;opacity:0;pointer-events:none;transition:opacity 0.3s;"></div>

    <div id="emp-sidebar" style="
        position: fixed;
        top: 0; right: 0;
        width: 400px;
        height: 100vh;
        background: white;
        z-index: 1101;
        box-shadow: -8px 0 40px rgba(0,0,0,0.18);
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    ">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #0d7a63 100%); color: white; padding: 1.25rem 1.25rem 1rem; flex-shrink: 0;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:0.7rem; opacity:0.8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Resumen Mensual</div>
                    <div id="esb-name" style="font-size:1.1rem; font-weight:800; line-height:1.2;">—</div>
                    <div id="esb-month" style="font-size:0.78rem; opacity:0.75; margin-top:3px;">{{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</div>
                </div>
                <button onclick="closeEmployeeSidebar()" style="background:rgba(255,255,255,0.15); border:none; color:white; width:30px; height:30px; border-radius:50%; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">✕</button>
            </div>

            <!-- Stat pills at top -->
            <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
                <div class="esb-pill" id="esb-pill-work">🗓 <span id="esb-total-work">0</span> turnos</div>
                <div class="esb-pill" id="esb-pill-rest">😴 <span id="esb-total-rest">0</span> libres</div>
                <div class="esb-pill" id="esb-pill-abs">🏥 <span id="esb-total-abs">0</span> ausencias</div>
            </div>
        </div>

        <!-- Scrollable body -->
        <div style="flex:1; overflow-y:auto; padding:1.1rem 1.25rem;">

            <!-- Turnos -->
            <div class="esb-section">
                <div class="esb-section-title">⏱ Tipos de Turno</div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Mañana (antes 12:00)</span><span class="esb-badge" id="esb-morning">0</span></div>
                    <div class="esb-day-list" id="esb-morning-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Tarde (12:00 en adelante)</span><span class="esb-badge" id="esb-afternoon">0</span></div>
                    <div class="esb-day-list" id="esb-afternoon-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Partido</span><span class="esb-badge esb-badge-orange" id="esb-partido">0</span></div>
                    <div class="esb-day-list" id="esb-partido-days"></div>
                </div>
            </div>

            <!-- Descansos -->
            <div class="esb-section">
                <div class="esb-section-title">📅 Descansos</div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Total libres</span><span class="esb-badge esb-badge-blue" id="esb-rest2">0</span></div>
                    <div class="esb-day-list" id="esb-rest2-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Domingos libres</span><span class="esb-badge esb-badge-blue" id="esb-sun-rest">0</span></div>
                    <div class="esb-day-list" id="esb-sun-rest-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Domingos trabajados</span><span class="esb-badge esb-badge-red" id="esb-sun-work">0</span></div>
                    <div class="esb-day-list" id="esb-sun-work-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Racha máx. sin descanso</span><span class="esb-badge" id="esb-streak">0</span></div>
                </div>
            </div>

            <!-- Ausencias -->
            <div class="esb-section" id="esb-abs-section">
                <div class="esb-section-title">🏥 Ausencias</div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Vacaciones</span><span class="esb-badge esb-badge-green" id="esb-vac">0</span></div>
                    <div class="esb-day-list" id="esb-vac-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Incapacidad</span><span class="esb-badge esb-badge-red" id="esb-inc">0</span></div>
                    <div class="esb-day-list" id="esb-inc-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Permiso</span><span class="esb-badge esb-badge-orange" id="esb-per">0</span></div>
                    <div class="esb-day-list" id="esb-per-days"></div>
                </div>
                <div class="esb-row-container">
                    <div class="esb-row"><span>Calamidad</span><span class="esb-badge esb-badge-red" id="esb-cal">0</span></div>
                    <div class="esb-day-list" id="esb-cal-days"></div>
                </div>
            </div>

            <!-- Áreas -->
            <div class="esb-section">
                <div class="esb-section-title">🏪 Días por Área</div>
                <div id="esb-areas">—</div>
            </div>

            <!-- Button -->
            <button
                id="esb-absence-btn"
                onclick=""
                style="width:100%; margin-top:0.75rem; padding:0.65rem 1rem; background:var(--primary); color:white; border:none; border-radius:8px; font-weight:600; font-size:0.85rem; cursor:pointer; transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'"
            >+ Registrar Novedad</button>
        </div>
    </div>

    <style>
        .esb-pill {
            background: rgba(255,255,255,0.18);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .esb-section {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .esb-section:last-child { border-bottom: none; }
        .esb-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--primary);
            opacity: 0.7;
            letter-spacing: 0.08em;
            padding-left: 0.4rem;
            border-left: 2px solid var(--primary);
            margin-bottom: 0.4rem;
        }
        .esb-row-container {
            margin-bottom: 0.5rem;
        }
        .esb-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.15rem 0.25rem;
            font-size: 0.82rem;
            color: #334155;
            border-radius: 5px;
            transition: background 0.1s;
        }
        .esb-day-list {
            font-size: 0.72rem;
            color: #64748b;
            padding-left: 0.25rem;
            margin-top: -1px;
            font-style: italic;
            line-height: 1.1;
        }
        .esb-row-container:hover .esb-row { background: #f8fafc; }
        .esb-badge {
            font-weight: 700;
            font-size: 0.78rem;
            color: var(--primary);
            background: rgba(0,128,100,0.08);
            padding: 2px 8px;
            border-radius: 20px;
            min-width: 24px;
            text-align: center;
        }
        .esb-badge-blue   { color: #0284c7; background: rgba(2,132,199,0.08); }
        .esb-badge-red    { color: #dc2626; background: rgba(220,38,38,0.08); }
        .esb-badge-green  { color: #16a34a; background: rgba(22,163,74,0.08); }
        .esb-badge-orange { color: #d97706; background: rgba(217,119,6,0.08); }

        #emp-sidebar::-webkit-scrollbar { width: 5px; }
        #emp-sidebar ::-webkit-scrollbar-track { background: #f8fafc; }
        #emp-sidebar ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }
    </style>

    <script>
        // Day-of-week map from thead data-weekday (0=Sun...6=Sat)
        const dayWeekdays = {};
        document.querySelectorAll('.day-header[data-weekday]').forEach(th => {
            const day = parseInt(th.textContent.trim().split(' ').pop()); // day number
            dayWeekdays[day] = parseInt(th.dataset.weekday);
        });

        function openEmployeeSidebar(empId, empName, nameEl) {
            // Get this employee's row
            const nameSpan = nameEl;
            const row = nameSpan.closest('tr');
            const cells = row.querySelectorAll('td.editable-cell');

            // Counters and Day lists
            let totalWork = 0, morning = 0, afternoon = 0, partido = 0;
            let totalRest = 0, sunRest = 0, sunWork = 0;
            let vac = 0, inc = 0, per = 0, cal = 0;
            
            const days_morning = [], days_afternoon = [], days_partido = [];
            const days_rest = [], days_sun_rest = [], days_sun_work = [];
            const days_vac = [], days_inc = [], days_per = [], days_cal = [];
            
            const areaCounts = {};
            const areaDays = {};
            const workStreak = [];

            cells.forEach(cell => {
                const day  = parseInt(cell.dataset.day);
                const type = (cell.dataset.type || '').toLowerCase();
                const area = (cell.dataset.area || '').toLowerCase();
                const abs  = (cell.dataset.absence || '').toUpperCase();
                const weekday = dayWeekdays[day]; // 0=Sun

                if (abs === 'VAC') { vac++; days_vac.push(day); workStreak.push(false); }
                else if (abs === 'INC') { inc++; days_inc.push(day); workStreak.push(false); }
                else if (abs === 'PER') { per++; days_per.push(day); workStreak.push(false); }
                else if (abs === 'CAL') { cal++; days_cal.push(day); workStreak.push(false); }
                else if (type === '' || type === 'ausencia') {
                    // Rest day (--)
                    totalRest++;
                    days_rest.push(day);
                    if (weekday === 0) { sunRest++; days_sun_rest.push(day); }
                    workStreak.push(false);
                } else {
                    // Working day
                    totalWork++;
                    if (weekday === 0) { sunWork++; days_sun_work.push(day); }
                    workStreak.push(true);

                    if (type === 'partido') {
                        partido++;
                        days_partido.push(day);
                    } else {
                        // Determine morning/afternoon from cell text
                        const text = cell.innerText.trim();
                        // Look for the first time entry like "7:00" or "07:00" or "12:30"
                        const hourMatch = text.match(/(\d{1,2}):\d{2}/);
                        if (hourMatch) {
                            const h = parseInt(hourMatch[1]);
                            // If hour is 7, 8, 9, 10, 11 -> Mañana
                            // If hour is 12, 1, 2, 3, 4, 5, 6 -> Tarde
                            if (h >= 7 && h <= 11) { morning++; days_morning.push(day); }
                            else { afternoon++; days_afternoon.push(day); }
                        }
                    }

                    // Area count
                    if (area) {
                        const aLabel = area.charAt(0).toUpperCase() + area.slice(1);
                        areaCounts[aLabel] = (areaCounts[aLabel] || 0) + 1;
                        if (!areaDays[aLabel]) areaDays[aLabel] = [];
                        areaDays[aLabel].push(day);
                    }
                }
            });

            // Max consecutive working days
            let maxStreak = 0, cur = 0;
            workStreak.forEach(w => { if (w) { cur++; maxStreak = Math.max(maxStreak, cur); } else cur = 0; });

            // Helper to format day list
            const fmtDays = (arr) => arr.length > 0 ? `Días: ${arr.join(', ')}` : '';

            // Populate sidebar
            document.getElementById('esb-name').textContent = empName;
            document.getElementById('esb-total-work').textContent = totalWork;
            document.getElementById('esb-total-rest').textContent = totalRest;
            document.getElementById('esb-total-abs').textContent = vac + inc + per + cal;

            document.getElementById('esb-morning').textContent   = morning;
            document.getElementById('esb-morning-days').textContent = fmtDays(days_morning);
            
            document.getElementById('esb-afternoon').textContent = afternoon;
            document.getElementById('esb-afternoon-days').textContent = fmtDays(days_afternoon);
            
            document.getElementById('esb-partido').textContent   = partido;
            document.getElementById('esb-partido-days').textContent = fmtDays(days_partido);
            
            document.getElementById('esb-rest2').textContent     = totalRest;
            document.getElementById('esb-rest2-days').textContent = fmtDays(days_rest);
            
            document.getElementById('esb-sun-rest').textContent  = sunRest;
            document.getElementById('esb-sun-rest-days').textContent = fmtDays(days_sun_rest);
            
            document.getElementById('esb-sun-work').textContent  = sunWork;
            document.getElementById('esb-sun-work-days').textContent = fmtDays(days_sun_work);
            
            document.getElementById('esb-streak').textContent    = maxStreak;
            
            document.getElementById('esb-vac').textContent = vac;
            document.getElementById('esb-vac-days').textContent = fmtDays(days_vac);
            
            document.getElementById('esb-inc').textContent = inc;
            document.getElementById('esb-inc-days').textContent = fmtDays(days_inc);
            
            document.getElementById('esb-per').textContent = per;
            document.getElementById('esb-per-days').textContent = fmtDays(days_per);
            
            document.getElementById('esb-cal').textContent = cal;
            document.getElementById('esb-cal-days').textContent = fmtDays(days_cal);

            // Areas
            const areasEl = document.getElementById('esb-areas');
            const sorted = Object.entries(areaCounts).sort((a,b) => b[1]-a[1]);
            if (sorted.length === 0) {
                areasEl.innerHTML = '<span style="color:#94a3b8;font-size:0.8rem;">Sin turnos con área registrada</span>';
            } else {
                areasEl.innerHTML = sorted.map(([a, c]) => `
                    <div class="esb-row-container">
                        <div class="esb-row">
                            <span>${a}</span>
                            <span class="esb-badge">${c} día${c!==1?'s':''}</span>
                        </div>
                        <div class="esb-day-list">${fmtDays(areaDays[a])}</div>
                    </div>`).join('');
            }

            // Absence button
            const absBtn = document.getElementById('esb-absence-btn');
            absBtn.onclick = () => { closeEmployeeSidebar(); openAbsenceModal(empId, empName); };

            // Open sidebar
            const sidebar  = document.getElementById('emp-sidebar');
            const overlay  = document.getElementById('emp-sidebar-overlay');
            sidebar.style.transform  = 'translateX(0)';
            overlay.style.opacity    = '1';
            overlay.style.pointerEvents = 'all';
        }

        function closeEmployeeSidebar() {
            document.getElementById('emp-sidebar').style.transform  = 'translateX(100%)';
            const ov = document.getElementById('emp-sidebar-overlay');
            ov.style.opacity = '0';
            ov.style.pointerEvents = 'none';
        }
    </script>

</div> <!-- End content-area -->
@endsection
