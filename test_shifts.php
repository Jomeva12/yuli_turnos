<?php
$empNames = ['Anuar Quintero', 'Juan Carlos', 'Lina Pe', 'Karina Hern', 'Sandri P'];
$employees = \App\Models\Employee::where(function($q) use ($empNames) {
    foreach($empNames as $n) {
        $q->orWhere('name', 'LIKE', "%$n%");
    }
})->get();

$monthStart = \Carbon\Carbon::now()->startOfMonth();
$monthEnd = \Carbon\Carbon::now()->endOfMonth();

foreach ($employees as $emp) {
    echo "====================================\n";
    echo "EMPLEADO: " . $emp->name . "\n";
    $shifts = \App\Models\Shift::where('employee_id', $emp->id)
        ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
        ->orderBy('date')
        ->get();
        
    $rests = 0;
    $sundays = 0;
    $mCount = 0;
    $tCount = 0;
    $pCount = 0;
    
    $weekShifts = [];
    
    foreach ($shifts as $s) {
        $date = \Carbon\Carbon::parse($s->date);
        $w = $date->weekOfMonth;
        
        $type = 'N/A';
        if ($s->type === 'descanso') {
            $type = 'DESC';
            $rests++;
            if ($date->isSunday()) $sundays++;
            if ($date->isSaturday()) echo "  [!] ERROR: Descanso en Sabado (".$s->date.")\n";
            $day = $date->day;
            if ($day >= 28 || $day <= 2) echo "  [!] ERROR: Descanso en Zona Roja (".$s->date.")\n";
        } elseif ($s->type === 'vacaciones') {
            $type = 'VAC';
        } elseif ($s->type === 'incapacidad') {
            $type = 'INC';
        } elseif ($s->type === 'partido') {
            $type = 'PARTIDO';
            $pCount++;
        } else {
            // guess
            if (preg_match('/^(\d{1,2}):\d{2}/', $s->schedule, $m)) {
                if (intval($m[1]) < 12) {
                    $type = 'MAÑANA';
                    $mCount++;
                } else {
                    $type = 'TARDE';
                    $tCount++;
                }
            }
        }
        
        $weekShifts[$w][] = $date->locale('es')->isoFormat('ddd DD') . ": " . str_pad($type, 7) . " (" . $s->schedule . ") [".$s->area_id."]";
    }
    
    foreach ($weekShifts as $w => $days) {
        echo " SEMANA $w:\n";
        foreach ($days as $d) {
            echo "   $d\n";
        }
    }
    
    echo " TOTALES:\n";
    echo "   Mañanas: $mCount\n";
    echo "   Tardes: $tCount\n";
    echo "   Partidos: $pCount\n";
    echo "   Descansos Totales: $rests (Domingos: $sundays)\n\n";
}
