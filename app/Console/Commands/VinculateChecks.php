<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\FEXPAR\Process\VinculoFederativoController;

class VinculateChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fexpar:vinculos {--pre-vinculate : First step of process} {--vinculate : Second step of process - Checking if is vinculated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Efetua os processos dos vinculos';

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
     * @return mixed
     */
    public function handle()
    {
        $vinculo_controller = new VinculoFederativoController;
        if($this->option('pre-vinculate')){
            $vinculo_controller->pre_vinculate();
        }elseif($this->option('vinculate')){
            $vinculo_controller->vinculate();
        }
    }
}
