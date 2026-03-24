<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ShiftController;
use Carbon\Carbon;

class GenerateShifts extends Command
{
    protected $signature = 'shift:generate';
    protected $description = 'Generate shifts for the current month locally to test fixes';

    public function handle()
    {
        $this->info("Generating shifts for the current month...");
        $monthStr = Carbon::now()->format('Y-m');
        $date = Carbon::parse($monthStr . '-01');
        $endOfMonth = $date->copy()->endOfMonth();

        $controller = new ShiftController();
        $reflection = new \ReflectionClass(ShiftController::class);
        $method = $reflection->getMethod('privateGenerateDay');
        $method->setAccessible(true);

        for ($d = $date->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $this->info("Generating " . $d->format('Y-m-d'));
            $method->invokeArgs($controller, [$d->format('Y-m-d')]);
        }
        $this->info("Done generating.");
    }
}
