<?php

use App\Models\Employee;
use App\Models\Area;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$employee = Employee::first();
$area = Area::first();

if (!$employee || !$area) {
    echo "No employees or areas found\n";
    exit;
}

echo "Testing toggle for Employee: {$employee->name}, Area: {$area->name}\n";

// Toggle ON
$employee->areas()->syncWithoutDetaching([$area->id]);
$hasOn = $employee->fresh()->areas->contains($area->id);
echo "Toggle ON: " . ($hasOn ? "SUCCESS" : "FAILED") . "\n";

// Toggle OFF
$employee->areas()->detach($area->id);
$hasOff = $employee->fresh()->areas->contains($area->id);
echo "Toggle OFF: " . (!$hasOff ? "SUCCESS" : "FAILED") . "\n";
