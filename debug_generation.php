<?php

use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\ShiftTemplate;
use App\Models\Area;
use Carbon\Carbon;

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function debugDay($dateStr) {
    $date = Carbon::parse($dateStr);
    $dayOfWeek = $date->dayOfWeekIso;
    echo "\n--- DEBUG PARA EL DIA: $dateStr (Día ISO: $dayOfWeek) ---\n";

    $templates = ShiftTemplate::where('day_of_week', $dayOfWeek)->with('area')->get();
    echo "Plantillas encontradas: " . $templates->count() . "\n";
    foreach ($templates->groupBy('area.name') as $areaName => $ts) {
        echo "  Área: $areaName (" . $ts->count() . " turnos)\n";
        foreach ($ts as $t) {
            echo "    - [{$t->type}] {$t->schedule}\n";
        }
    }

    $employees = Employee::with('areas')->get();
    echo "\nResumen de Empleados y sus Áreas habilitadas:\n";
    foreach ($employees as $emp) {
        $areaList = $emp->areas->pluck('name')->implode(', ');
        echo "  - {$emp->name}: [$areaList]\n";
    }

    // Simular lógica de Domingos Rotativos
    if ($dayOfWeek == 7) {
        echo "\nSimulando lógica de Domingos Rotativos...\n";
        // Aquí iría la lógica del controlador para ver quién 'debería' descansar
    }
}

// Ejecutar para el primer domingo de Abril 2026 (según screenshot)
debugDay('2026-04-05');
debugDay('2026-04-06'); // Lunes para comparar
