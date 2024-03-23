<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Console\Commands\UpdateStocksCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UpdateStocksCommand::class)->everyMinute()->withoutOverlapping();

