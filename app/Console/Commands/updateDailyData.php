<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Util\Helpers;

class updateDailyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily_data:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza diariamente los datos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "Ejecutado Actualización Diaria";
        return 0;
    }
}
