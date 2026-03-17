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
        
        <div style="display: flex; gap: 1rem;">
            <!-- Auto-Generate Day Form -->
            <form action="{{ route('shifts.generate_day') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($month)->format('Y-m-d') }}">
                <button type="submit" style="padding: 0.5rem 1rem; background-color: #10B981; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                    Generar Turnos (1 Día)
                </button>
            </form>
            
            <button style="padding: 0.5rem 1rem; background-color: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">Guardar Cambios</button>
        </div>
    </div>

    <div class="table-container">
        <table class="excel-table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php $currentDate = \Carbon\Carbon::parse($month)->setDay($i); @endphp
                        <th>{{ current(explode('-', $currentDate->format('D-d'))) }} {{ $i }}</th>
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
                                $currentDate = \Carbon\Carbon::parse($month)->setDay($i)->format('Y-m-d');
                                
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
                                
                                if($isAbsent) {
                                    $cellContent = $absenceType;
                                    $cellStyle = 'background-color: #FEE2E2; color: #DC2626; font-weight: bold; font-size: 0.8rem;';
                                } else if (isset($shifts[$employee->id])) {
                                    $shift = $shifts[$employee->id]->firstWhere('date', $currentDate);
                                    if ($shift) {
                                        $cellContent = str_replace('|', '<br>', $shift->schedule);
                                        if($shift->type == 'partido') {
                                            $cellStyle = 'background-color: #DBEAFE; color: #1E3A8A; font-size: 0.75rem; line-height: 1.2;';
                                        } else {
                                            $cellStyle = 'font-size: 0.75rem; line-height: 1.2;';
                                        }
                                    }
                                }
                            @endphp
                            
                            <td class="editable-cell" style="{{ $cellStyle }}" onclick="alert('Funcionalidad para editar el turno del día {{ $i }}')">
                                {!! $cellContent !!}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
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
