<?php

namespace App\Console\Commands\FEXPAR\Vinculos;

use App\Enxadrista;
use App\Vinculo;
use Illuminate\Console\Command;
use Str;

class ImportVinculosFromLastYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fexpar:vinculate:import-last-year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa os vínculos do ano passado como pré-vinculados para este ano.';

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
        foreach (Vinculo::where([["ano", "=", date("Y") - 1]])->where(function ($q) {
            $q->where([["is_confirmed_system","=",true]]);
            $q->orWhere([["is_confirmed_manually","=",true]]);
        })->get() as $vinculo_old) {
            $vinculo_old->is_efective = true;
            $vinculo_old->save();
        }
        foreach(Vinculo::where([["ano","=",date("Y")-1],["is_efective","=",true]])->get() as $vinculo_old){
            if(Vinculo::where([["ano", "=", date("Y")],["enxadrista_id","=", Enxadrista::getStaticId($vinculo_old->enxadrista_id)]])->count() == 0){
                $vinculo = $vinculo_old->replicate();
                $vinculo->enxadrista_id = Enxadrista::getStaticId($vinculo_old->enxadrista_id);
                $vinculo->uuid = Str::uuid();
                $vinculo->ano = date("Y");
                $vinculo->is_confirmed_system = false;
                $vinculo->is_confirmed_manually = false;
                $vinculo->system_inscricoes_in_this_club_confirmed = null;
                $vinculo->events_played = null;
                $vinculo->obs = null;
                $vinculo->vinculated_at = null;
                $vinculo->save();
            }else{
                $vinculo = Vinculo::where([["ano", "=", date("Y")], ["enxadrista_id", "=", $vinculo_old->enxadrista_id]])->first();

                if($vinculo->clube_id == $vinculo_old->clube_id && $vinculo_old->is_efective){
                    $vinculo->is_efective = true;
                    $vinculo->save();
                }
            }
        }
    }
}
