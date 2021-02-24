<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Util\Helpers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Menu\MenuController;
use Illuminate\Support\Facades\Log;

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
        $conexiones = DB::table("conexiones")->select("nombre")->get();
        foreach($conexiones as $connection) {
            $response = MenuController::build_menu_cadena($connection->nombre);
            Log::info($response);
        }
        Log::info("Tarea Diaria Completa");
        return "Tarea Diaria Completa";
    }
}
