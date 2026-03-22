<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\ShiftTemplate;
use Illuminate\Database\Seeder;

class ShiftTemplateSeeder extends Seeder
{
    public function run(): void
    {
        ShiftTemplate::truncate();

        $areas = Area::all()->pluck('id', 'name')->toArray();

        // 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun
        $config = [
            1 => [ // LUNES
                'Electrodomestico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:00', 'type' => 'partido'],
                ],
                'General' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '7:00-11:30|12:00-14:30', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:30-11:30|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '9:00-13:00|13:30-16:30', 'type' => 'normal'],
                    ['schedule' => '10:00-13:00|16:30-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '11:00-13:00|13:30-18:30', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '13:00-14:30|15:00-20:30', 'type' => 'normal'],
                    ['schedule' => '14:00-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:00|16:30-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '10:30-13:30|17:00-21:00', 'type' => 'partido'],
                ],
            ],
            2 => [ // MARTES
                'Electrodomestico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:00', 'type' => 'partido'],
                ],
                'Marking' => [
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                ],
                'General' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '7:00-11:30|12:00-14:30', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:30-11:30|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '9:00-13:00|13:30-16:30', 'type' => 'normal'],
                    ['schedule' => '10:00-13:00|16:30-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '11:00-13:00|13:30-18:30', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '13:00-14:30|15:00-20:30', 'type' => 'normal'],
                    ['schedule' => '14:00-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:00|16:30-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '10:30-13:30|17:00-21:00', 'type' => 'partido'],
                ],
            ],
            3 => [ // MIERCOLES
                'Electrodomestico' => [
                    ['schedule' => '7:00-13:00|13:30-15:00', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '13:30-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Valery Camacho' => [
                    ['schedule' => '7:00-11:00|15:00-19:00', 'type' => 'partido'],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-13:00|13:30-15:00', 'type' => 'normal'],
                    ['schedule' => '13:30-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-13:00|13:30-15:00', 'type' => 'normal'],
                    ['schedule' => '13:30-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:30', 'type' => 'partido'],
                ],
                'General' => [
                    ['schedule' => '7:00-12:00|12:30-15:00', 'type' => 'normal'],
                    ['schedule' => '7:00-12:30|13:00-15:00', 'type' => 'normal'],
                    ['schedule' => '7:30-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:00-11:30|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '8:30-13:30|14:00-16:30', 'type' => 'normal'],
                    ['schedule' => '9:00-13:30|14:00-17:00', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '10:00-14:00|14:30-18:00', 'type' => 'normal'],
                    ['schedule' => '10:00-13:30|17:00-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-14:00|14:30-18:30', 'type' => 'normal'],
                    ['schedule' => '11:00-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '12:30-14:00|14:30-20:00', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-21:00', 'type' => 'normal'],
                    ['schedule' => '13:30-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '13:30-16:00|16:30-21:10', 'type' => 'normal'],
                    ['schedule' => '13:30-16:00|16:30-21:10', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '9:00-13:30|14:00-17:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-20:00', 'type' => 'normal'],
                ],
            ],
            4 => [ // JUEVES
                'Electrodomestico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '6:30-11:00|11:30-14:00', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:00', 'type' => 'partido'],
                ],
                'Marking' => [
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                ],
                'General' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '7:00-11:30|12:00-14:30', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:30-11:30|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '9:00-13:00|13:30-16:30', 'type' => 'normal'],
                    ['schedule' => '10:00-13:00|16:30-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '11:00-13:00|13:30-18:30', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '13:00-14:30|15:00-20:30', 'type' => 'normal'],
                    ['schedule' => '14:00-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:00|16:30-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '10:30-13:30|17:00-21:00', 'type' => 'partido'],
                ],
            ],
            5 => [ // VIERNES
                'Electrodomestico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:00', 'type' => 'partido'],
                ],
                'General' => [
                    ['schedule' => '7:00-11:00|11:30-14:30', 'type' => 'normal'],
                    ['schedule' => '7:00-11:30|12:00-14:30', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:30-11:30|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '9:00-13:00|13:30-16:30', 'type' => 'normal'],
                    ['schedule' => '10:00-13:00|16:30-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-13:30|14:00-17:30', 'type' => 'normal'],
                    ['schedule' => '11:00-13:00|13:30-18:30', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '13:00-14:30|15:00-20:30', 'type' => 'normal'],
                    ['schedule' => '14:00-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:00|16:30-21:30', 'type' => 'normal'],
                    ['schedule' => '14:00-16:30|17:00-21:30', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '10:30-13:30|17:00-21:00', 'type' => 'partido'],
                ],
            ],
            6 => [ // SABADO
                'Electrodomestico' => [
                    ['schedule' => '7:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|14:00-18:00', 'type' => 'normal'],
                    ['schedule' => '13:00-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '7:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '13:00-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '7:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '13:00-16:00|16:30-21:30', 'type' => 'normal'],
                ],
                'Buffet' => [
                    ['schedule' => '11:00-14:00|17:00-21:30', 'type' => 'partido'],
                ],
                'General' => [
                    ['schedule' => '7:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '7:00-12:30|13:00-15:30', 'type' => 'normal'],
                    ['schedule' => '7:30-13:00|13:30-16:00', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|16:00-20:00', 'type' => 'partido'],
                    ['schedule' => '8:30-13:30|14:00-17:00', 'type' => 'normal'],
                    ['schedule' => '9:00-13:30|14:00-18:00', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|14:00-18:30', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|17:00-21:00', 'type' => 'partido'],
                    ['schedule' => '10:00-14:00|14:30-18:30', 'type' => 'normal'],
                    ['schedule' => '10:00-13:30|17:00-21:00', 'type' => 'partido'],
                    ['schedule' => '10:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '11:00-14:00|14:30-19:30', 'type' => 'normal'],
                    ['schedule' => '11:30-14:00|14:30-20:00', 'type' => 'normal'],
                    ['schedule' => '12:30-14:00|14:30-21:00', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-21:30', 'type' => 'normal'],
                    ['schedule' => '13:15-15:30|16:00-21:45', 'type' => 'normal'],
                    ['schedule' => '13:15-15:30|16:00-21:45', 'type' => 'normal'],
                    ['schedule' => '13:15-15:30|16:00-21:45', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '10:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:00-14:00|14:30-20:30', 'type' => 'normal'],
                ],
            ],
            7 => [ // DOMINGO
                'Electrodomestico' => [
                    ['schedule' => '8:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                ],
                'Domicilio' => [
                    ['schedule' => '8:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                ],
                'Cosmetico' => [
                    ['schedule' => '8:00-13:00|13:30-15:30', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                ],
                'General' => [
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '8:00-12:00|12:30-15:30', 'type' => 'normal'],
                    ['schedule' => '9:30-13:30|14:00-17:00', 'type' => 'normal'],
                    ['schedule' => '10:30-14:00|14:30-18:00', 'type' => 'normal'],
                    ['schedule' => '12:30-14:30|15:00-20:00', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                    ['schedule' => '13:00-15:30|16:00-20:30', 'type' => 'normal'],
                    // Comodines
                    ['schedule' => '11:30-14:00|14:30-19:00', 'type' => 'normal'],
                    ['schedule' => '12:30-14:30|15:00-20:00', 'type' => 'normal'],
                    ['schedule' => '10:00-14:00|14:30-17:30', 'type' => 'normal'],
                    ['schedule' => '11:00-14:30|15:00-18:30', 'type' => 'normal'],
                ],
            ],
        ];

        foreach ($config as $dayOfWeek => $areasInDay) {
            foreach ($areasInDay as $areaName => $shifts) {
                if (isset($areas[$areaName])) {
                    foreach ($shifts as $shift) {
                        ShiftTemplate::create([
                            'day_of_week' => $dayOfWeek,
                            'area_id' => $areas[$areaName],
                            'schedule' => $shift['schedule'],
                            'type' => $shift['type'],
                            'required_count' => 1,
                        ]);
                    }
                }
            }
        }
    }
}
