<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Cek keterlambatan dan buat denda otomatis setiap hari jam 00:01
        $schedule->command('check:keterlambatan')->dailyAt('00:01');
    }
} 