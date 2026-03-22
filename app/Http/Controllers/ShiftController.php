<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = \App\Models\Employee::with('areas')->orderBy('name')->get();
        $month = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($month);
        $daysInMonth = $carbonMonth->daysInMonth;

        $shifts = \App\Models\Shift::whereYear('date', $carbonMonth->year)
                                   ->whereMonth('date', $carbonMonth->month)
                                   ->get()
                                   ->groupBy('employee_id');

        $absences = \App\Models\Absence::where(function($q) use ($carbonMonth) {
            $q->whereMonth('start_date', $carbonMonth->month)
               ->orWhereMonth('end_date', $carbonMonth->month);
        })->get()->groupBy('employee_id');

        $areas = \App\Models\Area::orderBy('name')->get();

        $generationNotes = \App\Models\GenerationNote::whereYear('date', $carbonMonth->year)
                                                     ->whereMonth('date', $carbonMonth->month)
                                                     ->with('employee')
                                                     ->orderBy('date')
                                                     ->get();

        return view('shifts.index', compact('employees', 'month', 'daysInMonth', 'shifts', 'absences', 'areas', 'generationNotes'));
    }

    public function generateDay(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $this->privateGenerateDay($date);
        return back()->with('success', 'Turnos generados automáticamente para el día ' . $date);
    }

    public function generateRange(Request $request)
    {
        $startDate = $request->input('date', date('Y-m-d'));
        $days = $request->input('days', 14); // Default 2 weeks
        
        $carbonDate = \Carbon\Carbon::parse($startDate);
        
        for ($i = 0; $i < $days; $i++) {
            $this->privateGenerateDay($carbonDate->format('Y-m-d'));
            $carbonDate->addDay();
        }

        $this->verifyMonthCompliance($carbonDate->copy()->subDay()->format('Y-m'));
        return back()->with('success', "Turnos generados automáticamente para las próximas $days días.");
    }

    public function generateMonth(Request $request)
    {
        $monthStr = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($monthStr);
        $daysInMonth = $carbonMonth->daysInMonth;
        
        $startDate = $carbonMonth->startOfMonth()->format('Y-m-d');
        $carbonDate = \Carbon\Carbon::parse($startDate);
        
        for ($i = 0; $i < $daysInMonth; $i++) {
            $this->privateGenerateDay($carbonDate->format('Y-m-d'));
            $carbonDate->addDay();
        }

        $this->verifyMonthCompliance($monthStr);
        return back()->with('success', "Turnos generados automáticamente para todo el mes de " . $carbonMonth->translatedFormat('F Y'));
    }

    public function clearMonth(Request $request)
    {
        $monthStr = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($monthStr);
        
        \App\Models\Shift::whereYear('date', $carbonMonth->year)
                         ->whereMonth('date', $carbonMonth->month)
                         ->delete();

        \App\Models\GenerationNote::whereYear('date', $carbonMonth->year)
                                  ->whereMonth('date', $carbonMonth->month)
                                  ->delete();

        return back()->with('success', "Turnos y notas de " . $carbonMonth->translatedFormat('F Y') . " han sido eliminados.");
    }

    public function timeline($date = null)
    {
        $date = $date ?? date('Y-m-d');
        $carbonDate = \Carbon\Carbon::parse($date);
        
        $shifts = \App\Models\Shift::with(['employee', 'area'])
            ->whereDate('date', $date)
            ->get();

        $areas = \App\Models\Area::orderBy('name')->get();
        
        // Group shifts by area, separating General
        $shiftsByArea = [];
        $generalShifts = [];
        
        foreach ($shifts as $shift) {
            if ($shift->type === 'descanso') continue;
            
            $areaName = $shift->area ? strtolower($shift->area->name) : 'general';
            if ($areaName === 'general') {
                $generalShifts[] = $shift;
            } else {
                $shiftsByArea[$shift->area_id][] = $shift;
            }
        }

        return view('shifts.timeline', compact('date', 'carbonDate', 'areas', 'shiftsByArea', 'generalShifts'));
    }

    private function privateGenerateDay($date)
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        $monthStr = $carbonDate->format('Y-m');
        
        $startOfMonth = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $carbonDate->copy()->endOfMonth()->format('Y-m-d');

        // Eliminar turnos y notas previos de ese día para regenerar
        Shift::where('date', $date)->delete();
        \App\Models\GenerationNote::where('date', $date)->delete();

        // 1. Obtener empleados y ausencias
        $absences = \App\Models\Absence::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();
        
        $absentIds = $absences->pluck('employee_id')->toArray();
        foreach ($absences as $absence) {
            \App\Models\GenerationNote::create([
                'employee_id' => $absence->employee_id,
                'date' => $date,
                'message' => "Ausencia programada ({$absence->type})",
                'type' => 'info'
            ]);
        }

        $allEmployees = \App\Models\Employee::with('areas')->get();
        $availableEmployees = $allEmployees->whereNotIn('id', $absentIds);

        // 2. Contar descansos y partidos del mes/semana
        $monthlyRests = Shift::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('type', 'descanso')
            ->selectRaw('employee_id, count(*) as count')
            ->groupBy('employee_id')
            ->pluck('count', 'employee_id')
            ->toArray();

        $startOfWeek = $carbonDate->copy()->startOfWeek()->format('Y-m-d');
        $endOfWeek = $carbonDate->copy()->endOfWeek()->format('Y-m-d');
        $weeklySplits = Shift::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('type', 'partido')
            ->selectRaw('employee_id, count(*) as count')
            ->groupBy('employee_id')
            ->pluck('count', 'employee_id')
            ->toArray();

        // 3. Obtener la plantilla del día
        $dayNum = $dayOfWeek; // 1-7 (7=Dom)
        $dayTemplates = \App\Models\ShiftTemplate::with('area')->where('day_of_week', $dayNum)->get();

        // 4. Determinar cuántos descansos se necesitan hoy (según datos_proyecto.md)
        $restsQuota = [1=>4, 2=>3, 3=>0, 4=>3, 5=>4, 6=>0, 7=>10][$dayNum] ?? 0;
        
        $dayOfMonth = $carbonDate->day;
        $isRestrictedDay = ($dayOfMonth >= 28 || $dayOfMonth <= 2);

        // 5. Asignación de Turnos (Primero áreas específicas, luego General)
        $assignedIds = [];
        
        $specificTemplates = $dayTemplates->filter(fn($t) => !in_array($t->area->name, ['General', 'Comodin']));
        $generalTemplates = $dayTemplates->filter(fn($t) => in_array($t->area->name, ['General', 'Comodin']));

        // SI ES DOMINGO: Priorizar descansos rotativos
        if ($dayOfWeek == 7) {
            $sundayRests = \App\Models\Shift::where('type', 'descanso')
                ->whereYear('date', $carbonDate->year)
                ->whereMonth('date', $carbonDate->month)
                ->whereRaw('DAYOFWEEK(date) = 1')
                ->get()
                ->groupBy('employee_id');

            foreach ($availableEmployees->shuffle() as $emp) {
                if (count($assignedIds) >= $restsQuota) break;
                if (!isset($sundayRests[$emp->id])) {
                    $this->createRestShift($emp, $date, $isRestrictedDay);
                    $assignedIds[] = $emp->id;
                    $monthlyRests[$emp->id] = ($monthlyRests[$emp->id] ?? 0) + 1;
                }
            }
        }

        // Asignar áreas específicas - PRIORIDAD A ESPECIALISTAS (menos áreas primero)
        foreach ($specificTemplates as $template) {
            $eligible = $availableEmployees->filter(fn($emp) => 
                !in_array($emp->id, $assignedIds) && 
                $emp->areas->contains('id', $template->area_id)
            )->sortBy(fn($emp) => $emp->areas->count()); // Especialistas primero

            $selected = $eligible->first();
            if ($selected) {
                $this->createShift($selected, $template, $date);
                $assignedIds[] = $selected->id;
                if ($template->type === 'partido') {
                    $weeklySplits[$selected->id] = ($weeklySplits[$selected->id] ?? 0) + 1;
                }
            } else {
                \App\Models\GenerationNote::create([
                    'date' => $date,
                    'message' => "No hay empleados disponibles para {$template->area->name} ({$template->schedule})",
                    'type' => 'warning'
                ]);
            }
        }

        // Asignar General - Solo a quienes tengan la habilidad General
        foreach ($generalTemplates as $template) {
            // Filtrar por ID disponible y habilidad General
            $eligible = $availableEmployees->filter(fn($emp) => 
                !in_array($emp->id, $assignedIds) &&
                $emp->areas->contains('name', 'General')
            );

            // Si es turno partido, intentar evitar a quienes NO tengan el partido en su ADN de área principal
            // (Opcional, pero ayuda a la coherencia diaria)
            if ($template->type === 'partido') {
                $prefered = $eligible->filter(fn($emp) => 
                    !$emp->areas->contains(fn($area) => in_array($area->name, ['Cosmetico', 'Electrodomestico', 'Domicilio']))
                );
                if ($prefered->isNotEmpty()) $eligible = $prefered;
            }

            // Ordenar por menos áreas para dar prioridad a los menos versátiles que tengan General
            $selected = $eligible->sortBy(fn($emp) => $emp->areas->count())->first();

            if ($selected) {
                $this->createShift($selected, $template, $date);
                $assignedIds[] = $selected->id;
            } else {
                \App\Models\GenerationNote::create([
                    'date' => $date,
                    'message' => "No hay empleados disponibles para {$template->area->name} ({$template->schedule})",
                    'type' => 'warning'
                ]);
            }
        }

        // 6. Descansos restantes
        $unassigned = $allEmployees->whereNotIn('id', $assignedIds)->whereNotIn('id', $absentIds);
        foreach ($unassigned as $emp) {
            $restsCount = $monthlyRests[$emp->id] ?? 0;
            if ($restsCount < 4) {
                $this->createRestShift($emp, $date, $isRestrictedDay);
                $assignedIds[] = $emp->id;
                $monthlyRests[$emp->id] = $restsCount + 1;
            } else {
                \App\Models\GenerationNote::create([
                    'employee_id' => $emp->id,
                    'date' => $date,
                    'message' => "Empleado disponible sin turno asignado (Comodín)",
                    'type' => 'info'
                ]);
            }
        }
    }

    private function createRestShift($employee, $date, $isRestrictedDay)
    {
        Shift::create([
            'employee_id' => $employee->id,
            'date' => $date,
            'schedule' => 'DESCANSO',
            'type' => 'descanso',
            'area_id' => $employee->areas->first()->id ?? 1
        ]);

        if ($isRestrictedDay) {
            \App\Models\GenerationNote::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'message' => "Asignado descanso en fecha restringida (28-02)",
                'type' => 'warning'
            ]);
        }
    }

    private function createShift($employee, $template, $date)
    {
        return Shift::create([
            'employee_id' => $employee->id,
            'area_id' => $template->area_id,
            'date' => $date,
            'schedule' => $template->schedule,
            'type' => $template->type
        ]);
    }

    private function verifyMonthCompliance($monthStr)
    {
        $carbonMonth = \Carbon\Carbon::parse($monthStr);
        $start = $carbonMonth->copy()->startOfMonth()->format('Y-m-d');
        $end = $carbonMonth->copy()->endOfMonth()->format('Y-m-d');

        $employees = \App\Models\Employee::all();
        $rests = Shift::whereBetween('date', [$start, $end])
            ->where('type', 'descanso')
            ->selectRaw('employee_id, count(*) as count')
            ->groupBy('employee_id')
            ->pluck('count', 'employee_id');

        foreach ($employees as $emp) {
            $count = $rests[$emp->id] ?? 0;
            if ($count < 4) {
                \App\Models\GenerationNote::create([
                    'employee_id' => $emp->id,
                    'date' => $start, // Nota resumen al inicio del mes
                    'message' => "REPASO: Empleado solo cuenta con $count de 4 descansos requeridos este mes.",
                    'type' => 'warning'
                ]);
            }
        }

        // Verificar cobertura de áreas críticas
        $warnings = \App\Models\GenerationNote::whereBetween('date', [$start, $end])
            ->where('type', 'warning')
            ->where('message', 'like', 'No hay empleados disponibles%')
            ->count();

        if ($warnings > 0) {
            \App\Models\GenerationNote::create([
                'date' => $start,
                'message' => "REPASO: Se detectaron $warnings deficiencias de cobertura en el mes. Revisar notas diarias.",
                'type' => 'warning'
            ]);
        } else {
            \App\Models\GenerationNote::create([
                'date' => $start,
                'message' => "REPASO: Generación completada. Todos los puestos requeridos están cubiertos.",
                'type' => 'success'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
