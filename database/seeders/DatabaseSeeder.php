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
            'LINA PEÑA' => ['General', 'Cosmetico'],
            'MICHELL ROMERO' => ['General'],
            'DIANYS FANG' => ['General'],
            'ANGIE ARROYO' => ['General', 'Valery Camacho', 'Domicilio'],
            'MARIA CASTRO' => ['General', 'Domicilio'],
            'YENIS MEJIA' => ['General'],
            'ARIANA BUELVAS' => ['General'],
            'ADRIANA BUELVAS' => ['General', 'Buffet'],
            'ANUAR QUINTERO' => ['General'],
            'LAURA PACHECO' => ['General'],
            'MARIA PAULA' => ['General'],
            'MARIA PAULA GARCIA' => ['General'],
            'IVON RUIZ' => ['General', 'Buffet'],
            'STIVEN NIÑO' => ['General'],
            'MELISA MALDONADO' => ['General'],
            'CRISTINA GUTIERREZ' => ['General', 'Buffet'],
            'YENIFER REBOLLO' => ['General'],
            'LILIANA AREVALO' => ['Domicilio', 'General'],
            'SHAIRA GUERRERO' => ['Domicilio', 'General'],
            'JUAN CARLOS ARAGON' => ['Electrodomestico', 'General'],
            'AYLEEN GONZALEZ' => ['Electrodomestico'],
            'KARINA HERNANDEZ' => ['Electrodomestico'],
            'DUBIS VALLE' => ['Cosmetico', 'Domicilio'],
            'ESTEFANY ESCALANTE' => ['Cosmetico'],
            'KAREEN VARGAS' => ['General'],
            'KAREN VARGAS' => ['General'],
            'VIVIANA PADILLA' => ['General'],
            'SANDRI PEREZ' => ['Marking', 'General'],
            'JANEIBIS PUA' => ['General'],
            'RUTH CORTES' => ['General'],
            'LOHANA TABORDA' => ['General'],
            'MARIA DUQUE' => ['General', 'Domicilio'],
            'MARELIS CORREA' => ['General'],
        ];

        foreach ($employeeData as $empName => $skills) {
            $emp = Employee::create(['name' => $empName]);
            
            $areaIds = collect($skills)->map(function($skill) use ($areas) {
                return $areas[$skill]->id;
            })->toArray();
            
            $emp->areas()->sync($areaIds);
        }

        // 3. Plantillas de Turnos
        $this->seedTemplates($areas);
    }

    private function seedTemplates($areas)
    {
        $templatesConfig = [
            // MIERCOLES (3)
            3 => [
                'Valery Camacho' => [
                    ['schedule' => '7:00-11:00|3:30-7:00', 'type' => 'partido', 'count' => 1],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30', 'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-5:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'Marking' => [
                    ['schedule' => '7:30-12:00|12:30-3:30', 'type' => 'normal', 'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-11:30|4:00-8:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '9:00-1:00|4:30-8:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-1:30|5:00-9:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:00|12:30-3:00', 'type' => 'normal', 'count' => 2],
                    ['schedule' => '7:30-1:00|1:30-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '8:30-1:30|2:00-4:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-6:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '11:30-2:00|2:30-7:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-3:30|4:00-9:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-3:30|4:00-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 2],
                ]
            ],
            // VIERNES (5)
            5 => [
                 'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:00', 'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-11:00|11:30-2:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-5:00|5:30-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-3:30|4:00-8:30', 'type' => 'normal', 'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-11:00|11:30-2:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-5:00|5:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                 'Cosmetico' => [
                    ['schedule' => '7:00-11:00|11:30-2:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-5:00|5:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-11:00|4:00-8:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-1:00|4:30-8:30', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:30-1:30|5:00-9:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-1:30|2:00-5:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '11:00-2:00|2:30-6:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '12:00-2:00|2:30-7:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-3:30|4:00-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '2:00-4:30|5:00-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '7:00-11:00|11:30-2:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '7:00-11:30|12:00-2:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '8:30-12:30|1:00-4:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:00-1:30|2:00-4:30', 'type' => 'normal', 'count' => 1],
                ]
            ],
            // SABADO (6)
            6 => [
                'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30', 'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-10:00', 'type' => 'normal', 'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:30-1:00|1:30-6:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                 'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-12:00|4:00-8:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '9:00-1:00|5:00-9:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-2:00|5:00-9:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:30|1:00-3:30', 'type' => 'normal', 'count' => 2],
                    ['schedule' => '7:30-1:00|1:30-4:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '8:00-1:30|2:00-4:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:00-1:30|2:00-5:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-6:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '10:00-2:00|2:30-6:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-7:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '11:00-2:00|2:30-7:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '11:30-2:00|2:30-8:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '12:30-2:00|2:30-9:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-2:30|3:00-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:15-4:00|4:30-9:45', 'type' => 'normal', 'count' => 3],
                ]
            ],
            // DEFAULT (0)
            0 => [
                 'Buffet' => [
                    ['schedule' => '11:00-2:00|5:00-9:30', 'type' => 'partido', 'count' => 1],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-1:00|1:30-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-10:00', 'type' => 'normal', 'count' => 1],
                ],
                'Electrodomestico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                 'Cosmetico' => [
                    ['schedule' => '7:00-1:00|1:30-3:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:30-4:00|4:30-9:30', 'type' => 'normal', 'count' => 1],
                ],
                'General' => [
                    ['schedule' => '8:00-12:00|4:00-8:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '10:00-2:00|5:00-9:00', 'type' => 'partido', 'count' => 1],
                    ['schedule' => '7:00-12:30|1:00-3:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '7:30-1:00|1:30-4:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '8:00-1:30|2:00-4:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '9:30-1:30|2:00-6:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '10:30-2:00|2:30-7:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '11:00-2:00|2:30-7:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '12:30-2:00|2:30-9:00', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:00-2:30|3:00-9:30', 'type' => 'normal', 'count' => 1],
                    ['schedule' => '1:15-4:00|4:30-9:45', 'type' => 'normal', 'count' => 1],
                ]
            ]
        ];

        foreach ($templatesConfig as $dayOfWeek => $areaTemplates) {
            foreach ($areaTemplates as $areaName => $shifts) {
                if (isset($areas[$areaName])) {
                    foreach ($shifts as $shift) {
                        \App\Models\ShiftTemplate::create([
                            'day_of_week' => $dayOfWeek,
                            'area_id' => $areas[$areaName]->id,
                            'schedule' => $shift['schedule'],
                            'type' => $shift['type'],
                            'required_count' => $shift['count'],
                        ]);
                    }
                }
            }
        }
    }
}
