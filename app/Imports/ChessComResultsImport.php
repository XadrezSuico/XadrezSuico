<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use App\Evento;
use App\Torneio;
use App\InscricaoCriterioDesempate;

use App\Enum\ConfigType;

use Auth;
use Log;

class ChessComResultsImport  implements WithEvents, OnEachRow, WithHeadingRow
{
    private $event_id;
    private $tournament_id;
    private $event;
    private $tournament;

    private $players_not_found;
    private $players_not_found_result;

    public function __construct($event_id,$tournament_id){
        $this->event_id = $event_id;
        $this->event = Evento::find($event_id);
        $this->tournament = Torneio::find($tournament_id);

        $this->players_not_found = array();
        $this->players_not_found_result = array();

        $this->tournament->chesscom_setAllRegistrationsNotFound();
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(){
                activity()
                    ->performedOn($this->tournament)
                    ->causedBy(Auth::user())
                    ->log("Chess.com - Não encontrados como inscritos: ".json_encode($this->players_not_found));

                activity()
                    ->performedOn($this->tournament)
                    ->causedBy(Auth::user())
                    ->log("Chess.com - Não encontrados como inscritos em resultados: ".json_encode($this->players_not_found_result));
            },
        ];
    }

    public function onRow(Row $Row)
    {
        $row = $Row->toArray();

        Log::debug("ChessComPlayerResult: ".json_encode($row));


        // CHECK PRESENCE (----TO REVIEW----)
        if(isset($row["username"])){
            if($row["username"] != ""){
                $inscricao_count = $this->tournament->inscricoes()->whereHas("configs",function($q1) use ($row){
                    $q1->where([
                        ["key","=","chesscom_username"],
                        ["value_type","=",ConfigType::String],
                        ["string","=",mb_strtolower($row["username"])]
                    ]);
                })->count();
                if($inscricao_count > 0){
                    $inscricao = $this->tournament->inscricoes()->whereHas("configs",function($q1) use ($row){
                        $q1->where([
                            ["key","=","chesscom_username"],
                            ["value_type","=",ConfigType::String],
                            ["string","=",mb_strtolower($row["username"])]
                        ]);
                    })->first();

                    $inscricao->setConfig("chesscom_registration_found",ConfigType::Boolean,true);
                }else{
                    $this->players_not_found[] = $row["username"];
                }
            }
        }

        $inscricao_count = $this->tournament->inscricoes()->whereHas("configs",function($q1) use ($row){
            $q1->where([
                ["key","=","chesscom_username"],
                ["value_type","=",ConfigType::String],
                ["string","=",mb_strtolower($row["username"])]
            ]);
        })->count();
        if($inscricao_count > 0){
            $inscricao = $this->tournament->inscricoes()->whereHas("configs",function($q1) use ($row){
                $q1->where([
                    ["key","=","chesscom_username"],
                    ["value_type","=",ConfigType::String],
                    ["string","=",mb_strtolower($row["username"])]
                ]);
            })->first();

            $inscricao->pontos = $row["points"];
            $inscricao->confirmado = true;
            $inscricao->save();

            foreach($inscricao->criterios_desempate->all() as $criterio_desempate){
                $criterio_desempate->delete();
            }

            foreach($this->tournament->getCriterios() as $criterio_desempate){
                $criterio = new InscricaoCriterioDesempate;
                $criterio->inscricao_id = $inscricao->id;
                $criterio->criterio_desempate_id = $criterio_desempate->id;

                if(isset($criterio_desempate->code)){
                    $sheet_code = explode("chesscom-",$criterio_desempate->code)[1];

                    if(isset($row[$sheet_code])){
                        $criterio_desempate->valor = $row[$sheet_code];
                    }else{
                        $criterio->valor = 0;
                    }
                }else{
                    $criterio->valor = 0;
                }
                $criterio->save();
            }

        }else{
            $this->players_not_found_result[] = $row["username"];
        }

    }
}
