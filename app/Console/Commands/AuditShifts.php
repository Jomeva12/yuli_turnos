<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;

class AuditShifts extends Command
{
    protected $signature = 'shift:audit';
    protected $description = 'Audit shift generation for specific employees';

    public function handle()
    {
        $empNames = ['Anuar Quintero', 'Juan Carlos', 'Lina Pe', 'Karina Hern', 'Sandri P'];
        $employees = Employee::where(function($q) use ($empNames) {
            foreach($empNames as $n) {
                $q->orWhere('name', 'LIKE', "%$n%");
            }
        })->get();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        foreach ($employees as $emp) {
            $this->info("====================================");
            $this->info("EMPLEADO: " . $emp->name);
            
            $shifts = Shift::where('employee_id', $emp->id)
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
                $date = Carbon::parse($s->date);
                $w = $date->weekOfMonth;
                
                $type = 'N/A';
                if ($s->type === 'descanso') {
                    $type = 'DESC';
                    $rests++;
                    if ($date->isSunday()) $sundays++;
                    if ($date->isSaturday()) $this->error("  [!] ERROR: Descanso en Sabado (".$s->date.")");
                    $day = $date->day;
                    if ($day >= 28 || $day <= 2) $this->error("  [!] ERROR: Descanso en Zona Roja (".$s->date.")");
                } elseif ($s->type === 'vacaciones') {
                    $type = 'VAC';
                } elseif ($s->type === 'incapacidad') {
                    $type = 'INC';
                } elseif ($s->type === 'partido') {
                    $type = 'PARTIDO';
                    $pCount++;
                } else {
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
                
                $weekShifts[$w][] = $date->locale('es')->isoFormat('ddd DD') . " | " . str_pad($type, 7) . " | " . $s->schedule . " | Area: " . $s->area_id;
            }
            
            foreach ($weekShifts as $w => $days) {
                $this->line(" SEMANA $w:");
                foreach ($days as $d) {
                    $this->line("   $d");
                }
            }
            
            $this->line(" TOTALES:");
            $this->line("   Mañanas: $mCount");
            $this->line("   Tardes: $tCount");
            $this->line("   Partidos: $pCount");
            $this->line("   Descansos Totales: $rests (Domingos: $sundays)");
            $this->line("");
        }
    }
}
