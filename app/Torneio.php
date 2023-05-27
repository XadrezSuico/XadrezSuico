<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

use App\Enum\ConfigType;

use App\Software;
use App\TipoTorneio;
use App\CriterioDesempate;

use DateTime;

class Torneio extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'torneio';


    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if($model->uuid == NULL){
                $model->uuid = Str::uuid();
            }
        });

        // self::created(function($model){
        //     // ... code here
        // });

        self::updating(function($model){
            if($model->uuid == NULL){
                $model->uuid = Str::uuid();
            }
        });

        // self::updated(function($model){
        //     // ... code here
        // });

        // self::deleting(function($model){
        //     // ... code here
        // });

        // self::deleted(function($model){
        //     // ... code here
        // });
    }

    public function template()
    {
        return $this->belongsTo("App\TorneioTemplate", "torneio_template_id", "id");
    }
    public function evento()
    {
        return $this->belongsTo("App\Evento", "evento_id", "id");
    }
    public function tipo_torneio()
    {
        return $this->belongsTo("App\TipoTorneio", "tipo_torneio_id", "id");
    }
    public function software()
    {
        return $this->belongsTo("App\Software", "softwares_id", "id");
    }
    public function categorias()
    {
        return $this->hasMany("App\CategoriaTorneio", "torneio_id", "id");
    }
    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "torneio_id", "id");
    }
    public function rodadas()
    {
        return $this->hasMany("App\Rodada", "torneio_id", "id");
    }


    public function configs(){
        return $this->hasMany("App\TournamentConfig","torneio_id","id");
    }

    public function getCountInscritos()
    {
        return $this->inscricoes()->count();
    }
    public function getCountInscritosConfirmados()
    {
        return $this->inscricoes()->where([["confirmado", "=", true]])->count();
    }
    public function getCountInscritosConfirmadosWOs()
    {
        return $this->inscricoes()->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", true]])->count();
    }
    public function quantosInscritosPresentes()
    {
        return $this->getCountInscritosConfirmados() - $this->getCountInscritosConfirmadosWOs();
    }
    public function getCountInscritosNaoConfirmados()
    {
        return $this->inscricoes()->where([["confirmado", "=", false]])->count();
    }
    public function getCountLichessConfirmadosnoTorneio()
    {
        return $this->inscricoes()->where([["is_lichess_found", "=", true]])->count();
    }
    public function getCountCriterios()
    {
        if ($this->evento->criterios()->count() > 0) {
            return $this->evento->criterios()->count();
        }

        return $this->evento->grupo_evento->criterios()->count();
    }
    public function getCountCriteriosNaoManuais()
    {
        $torneio = $this;
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id],["evento_id","=",$this->evento->id]])->count() > 0) {
            Log::debug($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id],["evento_id","=",$this->evento->id]])->whereDoesntHave("criterio", function ($q1) use ($torneio) {
                $q1->where([
                    ["is_manual", "=", true],
                ]);
            })->count());
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id],["evento_id","=",$this->evento->id]])->whereDoesntHave("criterio", function ($q1) use ($torneio) {
                $q1->where([
                    ["is_manual", "=", true],
                ]);
            })->count();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id],["evento_id","=",$this->evento->id]])->whereDoesntHave("criterio", function ($q1) use ($torneio) {
            $q1->where([
                ["is_manual", "=", true],
            ]);
        })->count();
    }
    public function getCriteriosLista()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", false]]);})->orderBy("prioridade", "ASC")->get();
    }
    public function getCriterios()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->orderBy("prioridade", "ASC")->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", false]]);})->orderBy("prioridade", "ASC")->get();
    }
    public function getCriteriosManuais()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->orderBy("prioridade", "ASC")->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", true]]);})->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->where([["tipo_torneio_id", "=", $this->tipo_torneio_id]])->whereHas("criterio", function ($q1) {$q1->where([["is_manual", "=", true]]);})->orderBy("prioridade", "ASC")->get();
    }
    public function getCriteriosTotal()
    {
        if ($this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->count() > 0) {
            return $this->evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->orderBy("prioridade", "ASC")->get();
        }

        return $this->evento->grupo_evento->criterios()->where([["tipo_torneio_id", "=", $this->tipo_torneio_id],["softwares_id","=",$this->software->id]])->orderBy("prioridade", "ASC")->get();
    }
    public function getLastLichessPlayersUpdate(){
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->lichess_last_update);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i:s");
        }
        return "Não houve atualização ainda.";
    }
    public function getLastChessComPlayersUpdate(){
        if(!$this->hasConfig("chesscom_last_players_list_update")){
            return "Não houve atualização ainda.";
        }

        return date("d/m/Y H:i:s",$this->getConfig("chesscom_last_players_list_update",true));
    }
    public function findByTagCategoria($tag)
    {
        return Torneio::whereHas("categorias", function ($q0) use ($tag) {
            $q0->whereHas("categoria", function ($q1) use ($tag) {
                $q1->where([["code", "=", $tag]]);
            });
        })->first();
    }
    public function hasCriteriosDesempateNasInscricoes()
    {
        if ($this->inscricoes()
            ->whereNotNull("pontos")
            ->whereHas("criterios_desempate")
            ->count() > 0) {
            return true;
        }
        return false;
    }

    public function setAllInscricoesNotFound(){
        foreach($this->inscricoes->all() as $inscricao){
            $inscricao->is_lichess_found = false;
            $inscricao->save();
        }
    }

    public function getIsResultadosImportados(){
        if($this->tipo_torneio->id == 3){
            $rodada = $this->rodadas()->where([["numero","=",2]])->first();
            $emparceiramento = $rodada->emparceiramentos()->first();
            if(is_int($emparceiramento->resultado)){
                return "Sim";
            }
        }
        if($this->hasCriteriosDesempateNasInscricoes()){
            return "Sim";
        }
        return "Não";
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->categorias()->count() > 0 || $this->inscricoes()->count() > 0 || $this->rodadas()->count() > 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }


    public function export($type){
        switch($type){
            case "xadrezsuico":
                return $this->exportXadrezSuico();
            case "xadrezsuico-data":
                return $this->exportXadrezSuico(true);
        }

        return null;
    }

    public function exportXadrezSuico($send_data = false){
        $obj = array();

        if($this->uuid == NULL){
            $this->generateUuid();
        }

        $obj["uuid"] = $this->uuid;
        $obj["name"] = $this->name;
        switch($this->tipo_torneio->id){
            case 1:
                $obj["tournament_type"] =  "SWISS";
                break;
            case 2:
                $obj["tournament_type"] =  "SCHURING";
                break;
        }
        $obj["rounds_number"] = ($send_data) ? (($this->rodadas()->count() > 0) ? $this->rodadas()->count() : 0) : 0;
        $obj["table_start_number"] = 1;

        $obj["ordering_sequence"] = array();
        $obj["tiebreaks"] = array();

        $obj["categories"] = array();
        foreach($this->categorias->all() as $categoria_torneio){
            $obj["categories"][] = $categoria_torneio->export("xadrezsuico");
        }


        $obj["rounds"] = array();
        if($send_data){
            foreach($this->rodadas->all() as $rodada){
                $obj["rounds"][] = $rodada->export("xadrezsuico");
            }
        }

        $obj["players"] = array();
        foreach($this->inscricoes->all() as $inscricao){
            if($send_data){
                if($inscricao->confirmado){
                    $obj["players"][] = $inscricao->export("xadrezsuico");
                }
            }else{
                $obj["players"][] = $inscricao->export("xadrezsuico");
            }
        }

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }

    public function howManyPaid(){
        return $this->inscricoes()->where([["paid", "=", true]])->count();
    }
    public function howManyFree(){
        $categorias_id = $this->evento->categorias()->whereNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        return $this->inscricoes()->whereIn("categoria_id",$categorias_id)->count();;
    }
    public function howManyNotPaid(){
        $categorias_id = $this->evento->categorias()->whereNotNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        return $this->inscricoes()->where([["paid", "=", false]])->whereIn("categoria_id",$categorias_id)->count();
    }


    public function getConfigs(){
        return $this->configs->all();
    }

    public function hasConfig($key){
        if($this->configs()->where([["key","=",$key]])->count() > 0){
            return true;
        }
        return false;
    }
    public function getConfig($key,$return_value = false){
        if($this->hasConfig($key)){
            if($return_value){
                $tournament_config = $this->configs()->where([["key","=",$key]])->first();
                switch($tournament_config->value_type){
                    case ConfigType::Integer:
                        return $tournament_config->integer;
                    case ConfigType::Float:
                        return $tournament_config->float;
                    case ConfigType::Decimal:
                        return $tournament_config->decimal;
                    case ConfigType::Boolean:
                        return $tournament_config->boolean;
                    case ConfigType::String:
                        return $tournament_config->string;
                }
            }

            return ["ok"=>1,"error"=>0,"config"=>$this->configs()->where([["key","=",$key]])->first()];
        }
        if($return_value) return null;

        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }
    public function removeConfig($key){
        if($this->hasConfig($key)){
            $tournament_config = $this->configs()->where([["key","=",$key]])->first();

            $tournament_config->delete();

            return ["ok"=>1,"error"=>0];
        }
        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }

    public function setConfig($key,$type,$value){
        if($this->hasConfig($key)){
            $tournament_config = $this->configs()->where([["key","=",$key]])->first();

            if($tournament_config->value_type != $type){
                return ["ok"=>0,"error"=>1,"message"=>"O tipo do campo é diferente - ".$tournament_config->value_type." != ".$type];
            }
        }else{
            $tournament_config = new TournamentConfig;
            $tournament_config->torneio_id = $this->id;
            $tournament_config->key = $key;
            $tournament_config->value_type = $type;
        }

        switch($type){
            case ConfigType::Integer:
                $tournament_config->integer = $value;
                break;
            case ConfigType::Float:
                $tournament_config->float = $value;
                break;
            case ConfigType::Decimal:
                $tournament_config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $tournament_config->boolean = $value;
                break;
            case ConfigType::String:
                $tournament_config->string = $value;
                break;
        }

        $tournament_config->save();

        return ["ok"=>1,"error"=>0];
    }



    public function chesscom_setAllRegistrationsNotFound(){
        foreach($this->inscricoes->all() as $inscricao){
            if($inscricao->hasConfig("chesscom_registration_found")){
                $inscricao->removeConfig("chesscom_registration_found");
            }
        }
    }


    public function getClubsFromRegistrations($get_only_confirmed = false){
        $list = [];
        $registrations = array();

        if($get_only_confirmed){
            $registrations = $this->inscricoes()->where([["confirmado","=",true]])->get();
        }else{
            $registrations = $this->inscricoes->all();
        }

        foreach($registrations as $inscricao){
            if($inscricao->clube){
                $list[($inscricao->clube->id)] = $inscricao->clube;
            }
        }

        return $list;
    }


    public function checkIfIsChessCom(){
        if(Software::hasChessCom()){
            if($this->software->isChessCom()){
                $prioridade = 1;
                if($this->tipo_torneio->isSwiss()){
                    $tiebreaks = [
                        "chesscom-buchholz_cut_1",
                        "chesscom-buchholz",
                        "chesscom-sonneborn_berger",
                        "chesscom-direct_encounter",
                        "chesscom-the_greater_number_of_wins_including_forfeits",
                        "chesscom-number_of_wins_with_black_pieces",
                        "chesscom-aroc_1",
                    ];

                    foreach($this->evento->criterios->all() as $criterio){
                        if($criterio->tipo_torneio->isSwiss() && $criterio->software->isChessCom()){
                            $criterio->delete();
                        }
                    }

                    if(CriterioDesempate::whereIn("code",$tiebreaks)->count() >= 7){
                        foreach($tiebreaks as $tiebreak){
                            $criterio_desempate = CriterioDesempate::where([["is_chess_com","=",true],["code","=",trim($tiebreak)]])->first();

                            $criterio_desempate_evento = new CriterioDesempateEvento;
                            $criterio_desempate_evento->evento_id = $this->evento->id;
                            $criterio_desempate_evento->tipo_torneio_id = $this->tipo_torneio->id;
                            $criterio_desempate_evento->softwares_id = $this->software->id;
                            $criterio_desempate_evento->prioridade = $prioridade++;
                            $criterio_desempate_evento->criterio_desempate_id = $criterio_desempate->id;
                            $criterio_desempate_evento->save();
                        }
                    }
                }
            }
        }
    }
}
