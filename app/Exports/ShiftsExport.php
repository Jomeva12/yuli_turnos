<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShiftsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function view(): View
    {
        $carbonMonth = Carbon::parse($this->month);
        $daysInMonth = $carbonMonth->daysInMonth;

        $employees = Employee::with('areas')->orderBy('name')->get();
        
        $shifts = Shift::whereYear('date', $carbonMonth->year)
                       ->whereMonth('date', $carbonMonth->month)
                       ->with('area')
                       ->get()
                       ->groupBy('employee_id');

        $absences = Absence::where(function($q) use ($carbonMonth) {
            $q->whereMonth('start_date', $carbonMonth->month)
               ->orWhereMonth('end_date', $carbonMonth->month);
        })->get()->groupBy('employee_id');

        return view('exports.shifts', [
            'employees' => $employees,
            'shifts' => $shifts,
            'absences' => $absences,
            'month' => $this->month,
            'daysInMonth' => $daysInMonth
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            2 => ['font' => ['bold' => true]],
            'A:ZZ' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }
}
