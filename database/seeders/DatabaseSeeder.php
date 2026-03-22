<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\Employee;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Áreas
        $areaNames = [
            'General',
            'Valery Camacho',
            'Buffet',
            'Domicilio',
            'Electrodomestico',
            'Cosmetico',
            'Marking'
        ];

        $areas = [];
        foreach ($areaNames as $name) {
            $areas[$name] = Area::create(['name' => $name]);
        }

        // 2. Empleados y sus habilidades
        $employeeData = [
            'ANGIE ARROYO'        => ['General', 'Valery Camacho', 'Domicilio'],
            'ADRIANA BUELVAS'     => ['General', 'Buffet'],
            'ANUAR QUINTERO'      => ['General'],
            'AYLEEN GONZALEZ'     => ['Electrodomestico'],
            'CRISTINA GUTIERREZ'  => ['General', 'Buffet'],
            'DUBIS VALLE'         => ['Cosmetico', 'Domicilio'],
            'DIANYS FANG'         => ['General'],
            'ESTEFANY ESCALANTE'  => ['Cosmetico'],
            'IVON RUIZ'           => ['General', 'Buffet'],
            'JANEIBIS PUA'        => ['General'],
            'JUAN SEBASTIAN PIÑA' => ['General'],
            'JUAN CARLOS ARAGON'  => ['Electrodomestico', 'General'],
            'KAREN VARGAS'        => ['General'],
            'KARINA HERNÁNDEZ'    => ['Electrodomestico'],
            'LOHANA TABORDA'      => ['General'],
            'LINA PEÑA'           => ['General', 'Cosmetico'],
            'LAURA PACHECO'       => ['General'],
            'LILIANA AREVALO'     => ['Domicilio', 'General'],
            'MICHELL ROMERO'      => ['General'],
            'MARIA PAULA GARCIA'  => ['General'],
            'MELISA MALDONADO'    => ['General'],
            'MARIA CASTRO'        => ['General', 'Domicilio'],
            'MARIA DUQUE'         => ['General', 'Domicilio'],
            'SHAIRA GUERRERO'     => ['Domicilio', 'General'],
            'SANDRI PEREZ'        => ['Marking', 'General'],
            'RUTH CORTES'         => ['General'],
            'VIVIANA PADILLA'     => ['General'],
            'YENIS MEJIA'         => ['General'],
            'YENIFER REBOLLO'     => ['General'],
            'STIVEN NIÑO'         => ['General'],
            'MARELIS CORREA'      => ['General'],
        ];

        foreach ($employeeData as $empName => $skills) {
            $emp = Employee::create(['name' => $empName]);

            $areaIds = collect($skills)->map(function ($skill) use ($areas) {
                return $areas[$skill]->id;
            })->toArray();

            $emp->areas()->sync($areaIds);
        }

        // 3. Plantillas de Turnos
        $this->seedTemplates($areas);
    }

    private function seedTemplates($areas)
    {
        // =====================================================
        // PLANTILLA LUNES, MARTES, JUEVES, VIERNES (días 1,2,4,5)
        // Plantilla oficial del cliente - mínimo operativo
        // Partidos: turno con brecha horaria > 2 horas entre bloques
        // =====================================================
        $lmjvTemplate = [
            'General' => [
                ['schedule' => '7:00-11:00|11:30-2:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '7:00-11:00|12:00-2:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '8:00-12:00|12:30-3:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '8:30-11:30|4:00-8:00',   'type' => 'partido', 'count' => 1], // brecha ~4.5h
                ['schedule' => '9:00-1:00|1:30-4:30',    'type' => 'normal',  'count' => 1],
                ['schedule' => '10:30-1:30|1:30-6:00',   'type' => 'normal',  'count' => 1],
                ['schedule' => '10:00-1:00|4:30-8:30',   'type' => 'partido', 'count' => 1], // brecha ~3.5h
                ['schedule' => '10:00-1:00|5:00-9:00',   'type' => 'partido', 'count' => 1], // brecha ~4h
                ['schedule' => '10:30-1:30|5:00-9:00',   'type' => 'partido', 'count' => 1], // brecha ~3.5h
                ['schedule' => '11:30-2:00|2:30-7:00',   'type' => 'normal',  'count' => 1],
                ['schedule' => '1:00-3:30|4:00-8:30',    'type' => 'normal',  'count' => 2], // Ayleen + Laura
                ['schedule' => '2:00-3:30|4:00-9:30',    'type' => 'normal',  'count' => 1],
                ['schedule' => '2:00-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ['schedule' => '2:00-4:30|5:00-9:30',    'type' => 'normal',  'count' => 1],
            ],
            'Buffet' => [
                ['schedule' => '11:00-2:00|5:00-9:00',   'type' => 'partido', 'count' => 1], // brecha ~3h
            ],
            'Domicilio' => [
                ['schedule' => '7:00-11:00|11:30-2:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '2:00-5:00|5:30-9:30',    'type' => 'normal',  'count' => 1],
            ],
            'Electrodomestico' => [
                ['schedule' => '7:00-11:00|11:30-2:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '2:00-4:30|5:00-9:30',    'type' => 'normal',  'count' => 1],
            ],
            'Cosmetico' => [
                ['schedule' => '7:00-11:30|12:00-2:30',  'type' => 'normal',  'count' => 1],
                ['schedule' => '2:00-5:00|5:30-9:30',    'type' => 'normal',  'count' => 1],
            ],
            // Marking no aparece en el mínimo operativo de Lu/Ma/Ju/Vi
        ];

        $templatesConfig = [
            1 => $lmjvTemplate, // LUNES
            2 => $lmjvTemplate, // MARTES
            4 => $lmjvTemplate, // JUEVES
            5 => $lmjvTemplate, // VIERNES

            // MIERCOLES (3)
            3 => [
                'Valery Camacho' => [
                    ['schedule' => '7:00-11:00|3:30-7:00',   'type' => 'partido', 'count' => 1],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30',   'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-5:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'Marking' => [
                    ['schedule' => '7:30-12:00|12:30-3:30',  'type' => 'normal',  'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-11:30|4:00-8:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '9:00-1:00|4:30-8:00',    'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-1:30|5:00-9:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:00|12:30-3:00',  'type' => 'normal',  'count' => 2],
                    ['schedule' => '7:30-1:00|1:30-3:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '8:30-1:30|2:00-4:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-6:30',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '11:30-2:00|2:30-7:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:00-3:30|4:00-9:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-3:30|4:00-9:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 2],
                ],
            ],

            // SABADO (6)
            6 => [
                'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30',   'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-10:00',   'type' => 'normal',  'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '9:30-1:00|1:30-6:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:00-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:00-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-12:00|4:00-8:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '9:00-1:00|5:00-9:00',    'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-2:00|5:00-9:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:30|1:00-3:30',   'type' => 'normal',  'count' => 2],
                    ['schedule' => '7:30-1:00|1:30-4:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '8:00-1:30|2:00-4:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '9:00-1:30|2:00-5:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-6:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '10:00-2:00|2:30-6:30',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-7:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '11:00-2:00|2:30-7:30',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '11:30-2:00|2:30-8:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '12:30-2:00|2:30-9:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:00-2:30|3:00-9:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:15-4:00|4:30-9:45',    'type' => 'normal',  'count' => 3],
                ],
            ],

            // DEFAULT (0) - Domingo y fallback para días sin plantilla específica
            0 => [
                'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30',   'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-10:00',   'type' => 'normal',  'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30',    'type' => 'normal',  'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-12:00|4:00-8:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-2:00|5:00-9:00',   'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:30|1:00-3:30',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '7:30-1:00|1:30-4:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '8:00-1:30|2:00-4:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-6:00',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-7:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '11:00-2:00|2:30-7:30',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '12:30-2:00|2:30-9:00',   'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:00-2:30|3:00-9:30',    'type' => 'normal',  'count' => 1],
                    ['schedule' => '1:15-4:00|4:30-9:45',    'type' => 'normal',  'count' => 1],
                ],
            ],
        ];

        foreach ($templatesConfig as $dayOfWeek => $areaTemplates) {
            foreach ($areaTemplates as $areaName => $shifts) {
                if (isset($areas[$areaName])) {
                    foreach ($shifts as $shift) {
                        \App\Models\ShiftTemplate::create([
                            'day_of_week'    => $dayOfWeek,
                            'area_id'        => $areas[$areaName]->id,
                            'schedule'       => $shift['schedule'],
                            'type'           => $shift['type'],
                            'required_count' => $shift['count'],
                        ]);
                    }
                }
            }
        }
    }
}
