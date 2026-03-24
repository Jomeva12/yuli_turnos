<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Area;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('areas')->orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        return view('employees.index', compact('employees', 'areas'));
    }

    /**
     * Set or unset an area for an employee via AJAX.
     */
    public function toggleArea(Request $request)
    {
        Log::info('[ToggleArea] Request received', [
            'all' => $request->all(),
            'json' => $request->json()->all(),
            'content_type' => $request->header('Content-Type'),
        ]);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'area_id'     => 'required|exists:areas,id',
            'active'      => 'required',  // Relaxed — boolean JS may not pass strict Laravel boolean validation
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        // filter(null) handles both JSON true/false and "1"/"0" strings
        $active = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        Log::info('[ToggleArea] Processing', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'area_id'     => $request->area_id,
            'active_raw'  => $request->input('active'),
            'active_bool' => $active,
        ]);

        try {
            if ($active) {
                $employee->areas()->syncWithoutDetaching([$request->area_id]);
                Log::info('[ToggleArea] Area ATTACHED', ['employee' => $employee->name, 'area_id' => $request->area_id]);
            } else {
                $employee->areas()->detach($request->area_id);
                Log::info('[ToggleArea] Area DETACHED', ['employee' => $employee->name, 'area_id' => $request->area_id]);
            }
        } catch (\Exception $e) {
            Log::error('[ToggleArea] DB Error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'active'  => $active,
            'message' => $active ? 'Habilidad asignada correctamente' : 'Habilidad removida correctamente',
        ]);
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
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
