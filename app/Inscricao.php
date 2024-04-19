<?php

namespace App;

use App\Http\Controllers\External\XadrezSuicoPagController;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

use App\Enum\ConfigType;
use App\Helper\SingletonValueHelper;
use Illuminate\Support\Facades\Cache;
use Log;


class Inscricao extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'inscricao';


    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            Log::debug("self::creating");
            if($model->uuid == NULL){
                Log::debug("generating uuid");
                $model->uuid = Str::uuid();
                Log::debug("uuid: ".$model->uuid);
            }

            $torneio = Torneio::find($model->torneio_id);

            if(Inscricao::where([["enxadrista_id","=",$model->enxadrista_id]])->whereHas("torneio",function($q1) use ($torneio){
                $q1->where([["evento_id","=",$torneio->evento_id]]);
            })->count() > 0) {
                Log::debug("Inscrição do Enxadrista {$model->enxadrista_id} no Evento {$torneio->evento_id} impossibilitada - Já possui inscrição.");

                return false;
            }
        });

        self::created(function($model){
            if(
                $model->torneio->evento->isPaid()
            ){
                if($model->categoria->isPaid($model->torneio->evento->id)){
                    $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                    $xadrezsuicopag_controller->factory("registration")->register($model);
                }
            }

            $model->needToReplicateInfo();

            if(Cache::has($model->getCacheKey("registration_public_list"))){
                Cache::forget($model->getCacheKey("registration_public_list"));
            }
        });

        self::updating(function($model){
            Log::debug("self::updating");
            if($model->uuid == NULL){
                Log::debug("generating uuid");
                $model->uuid = Str::uuid();
                Log::debug("uuid: ".$model->uuid);
            }


            $model->needToReplicateInfo();
        });

        self::updated(function($model) {
            if (Cache::has($model->getCacheKey("registration_public_list"))) {
                Cache::forget($model->getCacheKey("registration_public_list"));
            }
        });

        self::deleting(function($model){
            if(!$model->isFree()){
                Log::debug("Deletando Inscricao - #{$model->id} - Inscrição paga");
                if ($model->getPaymentInfo("uuid")) {
                    Log::debug("Deletando Inscricao - #{$model->id} - Possui UUID");
                    $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                    $return = $xadrezsuicopag_controller->factory("registration")->delete($model->getPaymentInfo("uuid"));

                    if($return["ok"] == 1){
                        if(!$return["result"]){
                            Log::debug("Deletando Inscricao - #{$model->id} - Retorno falso - ".json_encode($return));
                            return false;
                        }else{
                            Log::debug("Deletando Inscricao - #{$model->id} - Retorno verdadeiro");
                        }
                    }else {
                        Log::debug("Deletando Inscricao - #{$model->id} - Sem retorno");
                    }
                }
                if($model->paid){
                    Log::debug("Deletando Inscricao - #{$model->id} - Já Paga");
                    return false;
                }
                Log::debug("Deletando Inscricao - #{$model->id} - Excluída");
            }
        });

        self::deleted(function($model) {
            if (Cache::has($model->getCacheKey("registration_public_list"))) {
                Cache::forget($model->getCacheKey("registration_public_list"));
            }
        });
    }

    public function enxadrista()
    {
        return $this->belongsTo("App\Enxadrista", "enxadrista_id", "id");
    }

    public function torneio()
    {
        return $this->belongsTo("App\Torneio", "torneio_id", "id");
    }

    public function categoria()
    {
        return $this->belongsTo("App\Categoria", "categoria_id", "id");
    }

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }

    public function from()
    {
        return $this->belongsTo("App\Inscricao", "inscricao_from", "id");
    }

    public function to()
    {
        return $this->hasMany("App\Inscricao", "inscricao_from", "id");
    }

    public function clube()
    {
        return $this->belongsTo("App\Clube", "clube_id", "id");
    }

    public function emparceiramentos_a()
    {
        return $this->hasMany("App\Emparceiramento", "inscricao_a", "id");
    }

    public function emparceiramentos_b()
    {
        return $this->hasMany("App\Emparceiramento", "inscricao_b", "id");
    }

    public function criterios_desempate()
    {
        return $this->hasMany("App\InscricaoCriterioDesempate", "inscricao_id", "id");
    }


    public function configs(){
        return $this->hasMany("App\RegistrationConfig","inscricao_id","id");
    }

    public function opcoes()
    {
        return $this->hasMany("App\CampoPersonalizadoOpcaoInscricao", "inscricao_id", "id");
    }

    public function hasOpcao($campo_personalizados_id)
    {
        // echo $this->id;
        // echo "\n";
        // print_r($this->opcoes->all());
        if($this->opcoes()->where([["campo_personalizados_id", "=", $campo_personalizados_id]])->count() > 0){
            return true;
        }
        return false;
    }

    public function getOpcao($campo_personalizados_id)
    {
        return $this->opcoes()->where([["campo_personalizados_id", "=", $campo_personalizados_id]])->first();
    }

    public function getLichessProcessLink(){
        return url("/inscricao/".$this->uuid."/lichess");
    }

    public function isDeletavel($only_required_checks = false, $check_payment = true)
    {
        if(!$only_required_checks){
            if($this->paid){
                return false;
            }
        }
        if($check_payment){
            if(!$this->isFree()){
                if(SingletonValueHelper::getInstance()->has($this->torneio->evento->xadrezsuicopag_uuid)){
                    Log::debug("SingletonValueHelper::has - true");
                    $singleton_value = SingletonValueHelper::getInstance();

                    if($singleton_value->get($this->torneio->evento->xadrezsuicopag_uuid)){
                        $results = $singleton_value->get($this->torneio->evento->xadrezsuicopag_uuid);
                        if(is_array($results)){
                            if(isset($results[$this->getPaymentInfo("uuid")])) {
                                if (!$results[$this->getPaymentInfo("uuid")]){
                                    Log::debug("SingletonValueHelper::get - registration uuid result = false");
                                    return false;
                                }else{
                                    Log::debug("SingletonValueHelper::get - registration uuid result = true");
                                }
                            }else{
                                Log::debug("SingletonValueHelper::get - has not the registration uuid");
                            }
                        }else {
                            Log::debug("SingletonValueHelper::get - is not array");
                        }
                    }
                }else{
                    Log::debug("SingletonValueHelper::has - false");
                    $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                    $return = $xadrezsuicopag_controller->factory("registration")->is_deletable($this->getPaymentInfo("uuid"));

                    if ($return["ok"] == 1) {
                        if (!$return["result"]) {
                            return false;
                        }
                    }
                }
            }
        }
        if ($this->id != null) {
            // if ($this->criterios_desempate()->count() == 0) {
                return true;
            // }
        }
        return false;
    }

    public function getCreatedAt()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i:s");
        }
        return false;
    }

    public function getEmparceiramentos()
    {
        $emparceiramentos = array();
        foreach($this->emparceiramentos_a->all() as $emparceiramento){
            $emparceiramentos[] = $emparceiramento;
        }
        foreach($this->emparceiramentos_b->all() as $emparceiramento){
            $emparceiramentos[] = $emparceiramento;
        }
        return $emparceiramentos;
    }

    public function getCidade(){
        $retorno = trim($this->cidade->name);
        if($this->cidade->estado->abbr){
            $retorno .= "/".trim($this->cidade->estado->abbr);
            if($this->cidade->estado->pais->codigo_iso){
                $retorno .= " - ".trim($this->cidade->estado->pais->codigo_iso);
            }
        }
        return $retorno;
    }

    public function hasInscricoesFromEstaParaGrupoEvento($grupo_evento_id){
        $inscricoes_count = $this->to()->whereHas("torneio",function($q1) use ($grupo_evento_id){
            $q1->whereHas("evento",function($q2) use ($grupo_evento_id){
                $q2->where([["grupo_evento_id","=",$grupo_evento_id]]);
            });
        })->count();
        if($inscricoes_count > 0) return true;
        return false;
    }

    public function hasInscricoesFromEstaParaEvento($evento_id){
        $inscricoes_count = $this->to()->whereHas("torneio",function($q1) use ($evento_id){
            $q1->where([["evento_id","=",$evento_id]]);
        })->count();
        if($inscricoes_count > 0) return true;
        return false;
    }

    public function getCategoriaTorneioUuid(){
        $categoria_torneio = $this->torneio->categorias()->where([["categoria_id","=",$this->categoria->id]])->first();
        return $categoria_torneio->uuid;
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
        $obj["name"] = $this->enxadrista->name;
        $obj["city"] = $this->cidade->export("xadrezsuico");
        $obj["club"] = ($this->clube) ? $this->clube->export("xadrezsuico") : ["uuid"=>"","name"=>""];
        $obj["category_uuid"] = $this->getCategoriaTorneioUuid();
        $obj["borndate"] = $this->enxadrista->getBorn();

        $obj["int_id"] = null;
        $obj["int_rating"] = null;

        $obj["xz_id"] = $this->enxadrista->id;
        if($this->torneio->evento->tipo_rating){
            $obj["xz_rating"] = $this->enxadrista->ratingParaEvento($this->torneio->evento->id,true);
        }else{
            $obj["xz_rating"] = null;
        }

        $obj["nat_id"] = null;
        $obj["nat_rating"] = null;

        if($this->torneio->evento->usa_cbx && !$this->torneio->evento->usa_lbx){
            $obj["nat_id"] = $this->enxadrista->cbx_id;
            $obj["nat_rating"] = $this->enxadrista->showRating(1, $this->torneio->evento->tipo_modalidade);
        }

        if(!$this->torneio->evento->usa_cbx && $this->torneio->evento->usa_lbx){
            $obj["nat_id"] = $this->enxadrista->lbx_id;
            $obj["nat_rating"] = $this->enxadrista->showRating(2, $this->torneio->evento->tipo_modalidade);
        }

        if($this->torneio->evento->usa_fide){
            $obj["fide_id"] = $this->enxadrista->fide_id;
            $obj["fide_rating"] = $this->enxadrista->showRating(0, $this->torneio->evento->tipo_modalidade, $this->torneio->evento->getConfig("fide_sequence"));
        }else{
            $obj["fide_id"] = null;
            $obj["fide_rating"] = null;
        }

        $obj["rounds_out"] = [];

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }


    public function getPaymentInfo($key){
        if($this->payment_info){
            $payment_info = json_decode($this->payment_info,true);

            if(isset($payment_info[$key])){
                return $payment_info[$key];
            }
        }

        return false;
    }

    public function isPresent(){
        if(!$this->confirmado){
            return false;
        }
        if($this->desconsiderar_pontuacao_geral){
            return false;
        }
        return true;
    }

    public function isFree(){
        if(!$this->torneio->evento->isPaid()){
            return true;
        }
        if(!$this->categoria->isPaid($this->torneio->evento->id)){
            return true;
        }
        return false;
    }

    public function registerToPayment(){
        if(
            $this->torneio->evento->isPaid()
        ){
            if($this->categoria->isPaid($this->torneio->evento->id)){
                $xadrezsuicopag_controller = XadrezSuicoPagController::getInstance();

                $xadrezsuicopag_controller->factory("registration")->register($this);
            }
        }
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
                $registration_config = $this->configs()->where([["key","=",$key]])->first();
                switch($registration_config->value_type){
                    case ConfigType::Integer:
                        return $registration_config->integer;
                    case ConfigType::Float:
                        return $registration_config->float;
                    case ConfigType::Decimal:
                        return $registration_config->decimal;
                    case ConfigType::Boolean:
                        return $registration_config->boolean;
                    case ConfigType::String:
                        return $registration_config->string;
                    case ConfigType::Date:
                        return $registration_config->date;
                    case ConfigType::DateTime:
                        return $registration_config->datetime;
                }
            }

            return ["ok"=>1,"error"=>0,"config"=>$this->configs()->where([["key","=",$key]])->first()];
        }
        if($return_value) return null;

        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }
    public function removeConfig($key){
        if($this->hasConfig($key)){
            $registration_config = $this->configs()->where([["key","=",$key]])->first();

            $registration_config->delete();

            return ["ok"=>1,"error"=>0];
        }
        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }

    public function setConfig($key,$type,$value){
        if($this->hasConfig($key)){
            $registration_config = $this->configs()->where([["key","=",$key]])->first();

            if($registration_config->value_type != $type){
                return ["ok"=>0,"error"=>1,"message"=>"O tipo do campo é diferente - ".$registration_config->value_type." != ".$type];
            }
        }else{
            $registration_config = new RegistrationConfig;
            $registration_config->inscricao_id = $this->id;
            $registration_config->key = $key;
            $registration_config->value_type = $type;
        }

        switch($type){
            case ConfigType::Integer:
                $registration_config->integer = $value;
                break;
            case ConfigType::Float:
                $registration_config->float = $value;
                break;
            case ConfigType::Decimal:
                $registration_config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $registration_config->boolean = $value;
                break;
            case ConfigType::String:
                $registration_config->string = $value;
                break;
            case ConfigType::Date:
                $registration_config->date = $value;
                break;
            case ConfigType::DateTime:
                $registration_config->datetime = $value;
                break;
        }

        $registration_config->save();

        return ["ok"=>1,"error"=>0];
    }

    public function needToReplicateInfo(){
        if($this->torneio){
            if($this->torneio->software->isChessCom()){
                if(!$this->hasConfig("chesscom_username")){
                    if($this->enxadrista){
                        if($this->enxadrista->chess_com_username){
                            $this->setConfig("chesscom_username",ConfigType::String,$this->enxadrista->chess_com_username);
                        }
                    }
                }else{
                    if($this->enxadrista){
                        if($this->enxadrista->chess_com_username){
                            $this->setConfig("chesscom_username",ConfigType::String,$this->enxadrista->chess_com_username);
                        }
                    }
                }
            }
        }
    }
}
