<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShiftsExport;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function manualIndex(Request $request)
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

        // Templates for the sidebar
        $allTemplates = \App\Models\ShiftTemplate::select('schedule', 'type')
                                              ->distinct()
                                              ->get();

        $groupedTemplates = $this->getGroupedTemplates();

        return view('shifts.manual_index', compact('employees', 'month', 'daysInMonth', 'shifts', 'absences', 'areas', 'groupedTemplates'));
    }

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

        $groupedTemplates = $this->getGroupedTemplates();

        return view('shifts.index', compact('employees', 'month', 'daysInMonth', 'shifts', 'absences', 'areas', 'generationNotes', 'groupedTemplates'));
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

        // --- PROTECCIÓN: No tocar empleados que ya tienen turnos manuales hoy ---
        $manualShiftEmployeeIds = Shift::where('date', $date)->where('is_manual', true)->pluck('employee_id')->toArray();
        $assignedIds = $manualShiftEmployeeIds; 
        $monthStr = $carbonDate->format('Y-m');
        
        $startOfMonth = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $carbonDate->copy()->endOfMonth()->format('Y-m-d');

        // Eliminar turnos y notas previos de ese día para regenerar (preservando los manuales)
        Shift::where('date', $date)->where('is_manual', false)->delete();
        \App\Models\GenerationNote::where('date', $date)->delete();

        // 1. Obtener empleados y ausencias
        $absences = \App\Models\Absence::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();
        
        $absentIds = $absences->pluck('employee_id')->toArray();
        $manualShiftIds = Shift::where('date', $date)->where('is_manual', true)->pluck('employee_id')->toArray();
        $unavailableIds = array_unique(array_merge($absentIds, $manualShiftIds));
        foreach ($absences as $absence) {
            \App\Models\GenerationNote::create([
                'employee_id' => $absence->employee_id,
                'date' => $date,
                'message' => "Ausencia programada ({$absence->type})",
                'type' => 'info'
            ]);
        }

        $allEmployees = \App\Models\Employee::with('areas')->get();
        $availableEmployees = $allEmployees->whereNotIn('id', $unavailableIds);

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

        // 4. Determinar cuántos descansos se necesitan hoy (Ajustado a 30 empleados)
        $dayOfMonth = $carbonDate->day;
        $isRestrictedDay = ($dayOfMonth >= 28 || $dayOfMonth <= 2);

        if ($isRestrictedDay) {
            $restsQuota = 0; // NINGÚN empleado descansa en zona roja
        } else {
            $restsQuota = [1=>5, 2=>4, 3=>0, 4=>4, 5=>5, 6=>0, 7=>12][$dayNum] ?? 0;
        }

        // 5. Asignación de Turnos (Primero áreas específicas, luego General)
        $assignedIds = [];
        
        $specificTemplates = $dayTemplates->filter(fn($t) => !in_array($t->area->name, ['General', 'Comodin']));
        $generalTemplates = $dayTemplates->filter(fn($t) => in_array($t->area->name, ['General', 'Comodin']));

        // --- NUEVA LÓGICA: IDENTIFICAR TARGET (MAÑANA/TARDE) DE LA SEMANA ---
        $getShiftTimeType = function($schedule) {
            if (preg_match('/^(\d{1,2}):\d{2}/', $schedule, $m)) {
                return intval($m[1]) < 12 ? 'mañana' : 'tarde';
            }
            return 'otro';
        };

        $startOfLastWeek = $carbonDate->copy()->subWeek()->startOfWeek()->format('Y-m-d');
        $endOfLastWeek = $carbonDate->copy()->subWeek()->endOfWeek()->format('Y-m-d');
        $startOfThisWeek = $carbonDate->copy()->startOfWeek()->format('Y-m-d');
        $yesterday = $carbonDate->copy()->subDay()->format('Y-m-d');

        $historicalShifts = \App\Models\Shift::whereBetween('date', [$startOfLastWeek, $yesterday])
            ->whereNotIn('type', ['descanso', 'ausencia'])
            ->get();

        $monthlyShifts = \App\Models\Shift::whereBetween('date', [$startOfMonth, $yesterday])
            ->whereNotIn('type', ['descanso', 'ausencia'])
            ->get();

        $targetTypeThisWeek = [];
        foreach ($availableEmployees as $emp) {
            $empShifts = $historicalShifts->where('employee_id', $emp->id);
            $lastWeek = $empShifts->where('date', '<=', $endOfLastWeek);

            // 1. Determinar target basado ESTRICTAMENTE en la semana anterior
            // (Evita que el algoritmo se tranque si el lunes se le fuerza un turno contrario)
            $lwM = $lwT = 0;
            foreach ($lastWeek as $s) {
                if ($s->type === 'partido') continue; // No cuentan para el balance de mañana/tarde
                $type = $getShiftTimeType($s->schedule);
                if ($type === 'mañana') $lwM++;
                if ($type === 'tarde') $lwT++;
            }

            if ($lwM > 0 || $lwT > 0) {
                // Rotación clásica: si tuvo más mañanas, le toca tarde.
                $targetTypeThisWeek[$emp->id] = ($lwM > $lwT) ? 'tarde' : 'mañana';
            } else {
                // 2. Si no laboró la semana pasada (o es inicio de mes), balancear mensual
                $empMonthShifts = $monthlyShifts->where('employee_id', $emp->id);
                $mM = $mT = 0;
                foreach ($empMonthShifts as $s) {
                    if ($s->type === 'partido') continue;
                    $type = $getShiftTimeType($s->schedule);
                    if ($type === 'mañana') $mM++;
                    if ($type === 'tarde') $mT++;
                }

                if ($mM == 0 && $mT == 0) {
                    // Distribución equitativa inicial basada en ID
                    $targetTypeThisWeek[$emp->id] = ($emp->id % 2 == 0) ? 'mañana' : 'tarde';
                } else {
                    $targetTypeThisWeek[$emp->id] = ($mM > $mT) ? 'tarde' : 'mañana';
                }
            }
        }

        // --- NUEVA LÓGICA: ASIGNAR DESCANSOS PRIMERO ---
        // Obtener descansos de domingos para rotación
        $sundayRests = [];
        if ($dayOfWeek == 7) {
            $sundayRests = \App\Models\Shift::where('type', 'descanso')
                ->whereYear('date', $carbonDate->year)
                ->whereMonth('date', $carbonDate->month)
                ->whereRaw('DAYOFWEEK(date) = 1')
                ->get()
                ->groupBy('employee_id')
                ->map(fn($group) => count($group))
                ->toArray();
        }

        // Ordenar candidatos para descanso
        $restCandidates = $availableEmployees->sort(function($a, $b) use ($monthlyRests, $dayOfWeek, $sundayRests) {
            if ($dayOfWeek == 7) {
                $aSun = isset($sundayRests[$a->id]) ? 1 : 0;
                $bSun = isset($sundayRests[$b->id]) ? 1 : 0;
                if ($aSun != $bSun) return $aSun <=> $bSun;
            }
            $aRests = $monthlyRests[$a->id] ?? 0;
            $bRests = $monthlyRests[$b->id] ?? 0;
            return $aRests <=> $bRests;
        });

        $assignedRests = 0;
        $simulatedPool = clone $availableEmployees;

        foreach ($restCandidates as $emp) {
            if ($assignedRests >= $restsQuota) break;
            if (($monthlyRests[$emp->id] ?? 0) >= 4) continue; // Ya tiene sus 4 descansos

            // Probar si retirando a este empleado aún podemos cubrir las áreas específicas
            $tempPool = $simulatedPool->reject(fn($e) => $e->id == $emp->id);

            if ($this->canSatisfyTemplates($tempPool, $specificTemplates)) {
                $this->createRestShift($emp, $date, $isRestrictedDay);
                $assignedIds[] = $emp->id;
                $monthlyRests[$emp->id] = ($monthlyRests[$emp->id] ?? 0) + 1;
                $simulatedPool = $tempPool;
                $assignedRests++;
            }
        }

        // Asignar áreas específicas - PRIORIDAD A ESPECIALISTAS (menos áreas primero)
        foreach ($specificTemplates as $template) {
            $eligible = $availableEmployees->filter(fn($emp) => 
                !in_array($emp->id, $assignedIds) && 
                $emp->areas->contains('id', $template->area_id)
            );

            $templateTimeType = $getShiftTimeType($template->schedule);
            
            $selected = $eligible->sortBy(function($emp) use ($templateTimeType, $targetTypeThisWeek, $template, $weeklySplits) {
                if ($template->type === 'partido') {
                    $splitsCount = $weeklySplits[$emp->id] ?? 0;
                    $isSpecialist = $emp->areas->contains(fn($area) => in_array($area->name, ['Cosmetico', 'Electrodomestico', 'Domicilio'])) ? 1 : 0;
                    return sprintf("%04d-%d-%04d", $splitsCount, $isSpecialist, $emp->areas->count());
                } else {
                    $match = ($templateTimeType === 'otro' || ($targetTypeThisWeek[$emp->id] ?? '') === $templateTimeType) ? 0 : 1;
                    return sprintf("%d-%04d", $match, $emp->areas->count());
                }
            })->first();

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

            $templateTimeType = $getShiftTimeType($template->schedule);
            
            // Ordenar por Match de Rotación y Luego por menos áreas. Para partidos, equilibrar carga.
            $selected = $eligible->sortBy(function($emp) use ($templateTimeType, $targetTypeThisWeek, $template, $weeklySplits) {
                if ($template->type === 'partido') {
                    $splitsCount = $weeklySplits[$emp->id] ?? 0;
                    $isSpecialist = $emp->areas->contains(fn($area) => in_array($area->name, ['Cosmetico', 'Electrodomestico', 'Domicilio'])) ? 1 : 0;
                    return sprintf("%04d-%d-%04d", $splitsCount, $isSpecialist, $emp->areas->count());
                } else {
                    $match = ($templateTimeType === 'otro' || ($targetTypeThisWeek[$emp->id] ?? '') === $templateTimeType) ? 0 : 1;
                    return sprintf("%d-%04d", $match, $emp->areas->count());
                }
            })->first();

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

        // 6. Descansos restantes o Turnos Comodín
        $unassigned = $allEmployees->whereNotIn('id', $assignedIds)->whereNotIn('id', $absentIds);
        foreach ($unassigned as $emp) {
            $restsCount = $monthlyRests[$emp->id] ?? 0;
            if ($restsCount < 4 && !$isRestrictedDay) {
                // Solo si NO es día rojo permitimos rellenar con descansos
                $this->createRestShift($emp, $date, $isRestrictedDay);
                $assignedIds[] = $emp->id;
                $monthlyRests[$emp->id] = $restsCount + 1;
                $assignedRests++;
            } else {
                // Al no poder darle más descansos, lo asignamos como refuerzo (turno extra)
                // Buscamos una plantilla de General que coincida con su rotación semanal
                $target = $targetTypeThisWeek[$emp->id] ?? 'mañana';
                $fallbackTemplate = $generalTemplates->sortBy(function($t) use ($getShiftTimeType, $target) {
                    return $getShiftTimeType($t->schedule) === $target ? 0 : 1;
                })->first();

                if ($fallbackTemplate) {
                    $this->createShift($emp, $fallbackTemplate, $date);
                    $assignedIds[] = $emp->id;
                    \App\Models\GenerationNote::create([
                        'employee_id' => $emp->id,
                        'date' => $date,
                        'message' => $isRestrictedDay ? "Asignado como refuerzo (Zona Roja prohíbe descanso)" : "Asignado como refuerzo (Comodín extra)",
                        'type' => 'info'
                    ]);
                }
            }
        }
    }

    private function canSatisfyTemplates($pool, $templates) {
        $usedIds = [];
        foreach ($templates as $t) {
            $eligible = $pool->filter(function($e) use ($t, $usedIds) {
                return !in_array($e->id, $usedIds) && $e->areas->contains('id', $t->area_id);
            });
            if ($eligible->isEmpty()) return false;
            
            $selected = $eligible->sortBy(fn($e) => $e->areas->count())->first();
            $usedIds[] = $selected->id;
        }
        return true;
    }

    private function createRestShift($employee, $date, $isRestrictedDay)
    {
        Shift::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            [
                'schedule' => 'DESCANSO',
                'type' => 'descanso',
                'area_id' => $employee->areas->first()->id ?? 1,
                'is_manual' => false
            ]
        );

        if ($isRestrictedDay) {
            \App\Models\GenerationNote::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'message' => "Descanso asignado por alta demanda (días 28 al 02)",
                'type' => 'warning'
            ]);
        }
    }

    private function createShift($employee, $template, $date)
    {
        return Shift::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            [
                'area_id' => $template->area_id,
                'schedule' => $template->schedule,
                'type' => $template->type,
                'is_manual' => false
            ]
        );
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

    public function manualAssignment(\App\Models\Employee $employee, Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $carbonMonth = \Carbon\Carbon::parse($month);
        $daysInMonth = $carbonMonth->daysInMonth;

        $shifts = \App\Models\Shift::where('employee_id', $employee->id)
                                   ->whereYear('date', $carbonMonth->year)
                                   ->whereMonth('date', $carbonMonth->month)
                                   ->get()
                                   ->keyBy(fn($s) => \Carbon\Carbon::parse($s->date)->day);

        $absences = \App\Models\Absence::where('employee_id', $employee->id)
                                       ->where(function($q) use ($carbonMonth) {
                                           $q->whereBetween('start_date', [$carbonMonth->copy()->startOfMonth(), $carbonMonth->copy()->endOfMonth()])
                                             ->orWhereBetween('end_date', [$carbonMonth->copy()->startOfMonth(), $carbonMonth->copy()->endOfMonth()])
                                             ->orWhere(function($sq) use ($carbonMonth) {
                                                 $sq->where('start_date', '<=', $carbonMonth->copy()->startOfMonth())
                                                    ->where('end_date', '>=', $carbonMonth->copy()->endOfMonth());
                                             });
                                       })
                                       ->get();

        $allTemplates = \App\Models\ShiftTemplate::select('schedule', 'type')
                                              ->distinct()
                                              ->get();

        $groupedTemplates = [
            'mañana' => [],
            'tarde' => [],
            'partido' => [],
            'otros' => []
        ];

        foreach ($allTemplates as $t) {
            if ($t->type == 'partido') {
                $groupedTemplates['partido'][] = $t;
            } else if ($t->schedule == 'DESCANSO') {
                $groupedTemplates['otros'][] = $t;
            } else {
                // Determine by first hour in schedule
                $match = [];
                if (preg_match('/(\d{1,2}):\d{2}/', $t->schedule, $match)) {
                    $hour = intval($match[1]);
                    if ($hour >= 5 && $hour <= 11) {
                        $groupedTemplates['mañana'][] = $t;
                    } else {
                        $groupedTemplates['tarde'][] = $t;
                    }
                } else {
                    $groupedTemplates['otros'][] = $t;
                }
            }
        }

        $areas = \App\Models\Area::orderBy('name')->get();

        return view('shifts.manual_assignment', compact('employee', 'month', 'daysInMonth', 'shifts', 'groupedTemplates', 'areas', 'absences'));
    }

    public function export(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        return Excel::download(new ShiftsExport($month), "turnos_{$month}.xlsx");
    }

    public function manualAssign(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'schedule' => 'nullable|string',
            'type' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $employeeId = $request->employee_id;
        $date = $request->date;

        if (!$request->schedule || $request->schedule == 'NONE') {
            Shift::where('employee_id', $employeeId)->where('date', $date)->delete();
            return response()->json(['success' => true, 'message' => 'Turno eliminado']);
        }

        $shift = Shift::updateOrCreate(
            ['employee_id' => $employeeId, 'date' => $date],
            [
                'area_id' => $request->area_id ?? 1, // Default area
                'schedule' => $request->schedule,
                'type' => $request->type ?? 'normal',
                'is_manual' => true
            ]
        );

        $shift->load('area');
        return response()->json(['success' => true, 'shift' => $shift]);
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
    private function getGroupedTemplates()
    {
        $allTemplates = \App\Models\ShiftTemplate::select('schedule', 'type')
                                              ->distinct()
                                              ->get();

        $groupedTemplates = [
            'mañana' => [],
            'tarde' => [],
            'partido' => [],
            'otros' => []
        ];

        foreach ($allTemplates as $t) {
            if ($t->type == 'partido') {
                $groupedTemplates['partido'][] = $t;
            } else if ($t->schedule == 'DESCANSO') {
                $groupedTemplates['otros'][] = $t;
            } else {
                $match = [];
                if (preg_match('/(\d{1,2}):\d{2}/', $t->schedule, $match)) {
                    $hour = intval($match[1]);
                    if ($hour >= 5 && $hour <= 11) {
                        $groupedTemplates['mañana'][] = $t;
                    } else {
                        $groupedTemplates['tarde'][] = $t;
                    }
                } else {
                    $groupedTemplates['otros'][] = $t;
                }
            }
        }
        return $groupedTemplates;
    }
}
