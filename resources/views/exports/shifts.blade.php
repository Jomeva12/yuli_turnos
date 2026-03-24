@php
    $carbonMonth = \Carbon\Carbon::parse($month);
    $monthName = strtoupper($carbonMonth->translatedFormat('F'));
@endphp
<table>
    <thead>
    <!-- Row 1: Month and General Title -->
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000;">{{ $monthName }}</th>
        <th style="border: 1px solid #000000;"></th>
        <th style="font-weight: bold; border: 1px solid #000000; text-align: center;">CAJEROS</th>
        @for ($i = 1; $i <= $daysInMonth; $i++)
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center;">{{ $i }}</th>
        @endfor
    </tr>
    <!-- Row 2: Full Date Column Headers -->
    <tr>
        <th style="border: 1px solid #000000;"></th>
        <th style="border: 1px solid #000000;"></th>
        <th style="border: 1px solid #000000;"></th>
        @for ($i = 1; $i <= $daysInMonth; $i++)
            @php 
                $date = \Carbon\Carbon::parse($month)->setDay($i);
                $isSunday = $date->dayOfWeek == 0;
                $color = $isSunday ? '#FF0000' : '#000000';
            @endphp
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; color: {{ $color }};">
                {{ strtoupper($date->translatedFormat('l j')) }}
            </th>
        @endfor
    </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        <tr>
            <!-- Column A: Fixed ID/Store -->
            <td style="border: 1px solid #000000; font-size: 9pt;">1203 - SAO - Santamarta</td>
            <!-- Column B: Area -->
            <td style="border: 1px solid #000000; font-size: 9pt;">
                {{ $employee->areas->first()->name ?? 'Caja Lineal' }}
            </td>
            <!-- Column C: Name -->
            <td style="border: 1px solid #000000; font-weight: bold;">{{ strtoupper($employee->name) }}</td>
            
            @for ($i = 1; $i <= $daysInMonth; $i++)
                @php 
                    $currentDate = \Carbon\Carbon::parse($month)->setDay($i)->format('Y-m-d');
                    $cellContent = '';
                    
                    // Check for Absence
                    if(isset($absences[$employee->id])) {
                        foreach($absences[$employee->id] as $absence) {
                            if($currentDate >= $absence->start_date && $currentDate <= $absence->end_date) {
                                $cellContent = strtoupper($absence->type); 
                                break;
                            }
                        }
                    }

                    // Check for Shift if no absence
                    if ($cellContent == '' && isset($shifts[$employee->id])) {
                        $shift = $shifts[$employee->id]->firstWhere('date', $currentDate);
                        if ($shift) {
                            if ($shift->type === 'descanso') {
                                $cellContent = 'DESCANSO';
                            } else {
                                $cellContent = $shift->schedule;
                            }
                        }
                    }
                @endphp
                <td style="border: 1px solid #000000; text-align: center; vertical-align: center;">
                    {{ $cellContent }}
                </td>
            @endfor
        </tr>
    @endforeach
    </tbody>
</table>
