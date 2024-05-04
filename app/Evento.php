<?php

namespace App;

use App\Enxadrista;
use App\TipoRatingRegras;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

use App\Enum\ConfigType;

use DB;

class Evento extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'evento';


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

        self::updated(function($model){
            foreach($model->torneios->all() as $torneio){
                foreach($torneio->inscricoes->all() as $inscricao){
                    if($inscricao->hasCache("v1_public_list")) $inscricao->forgetCache("v1_public_list");
                }
            }
        });

        // self::deleting(function($model){
        //     // ... code here
        // });

        // self::deleted(function($model){
        //     // ... code here
        // });
    }

    // AQUELE Evento CLASSIFICA PARA ESTE Evento
    public function classificador()
    {
        return $this->belongsTo("App\Evento", "evento_classificador_id", "id");
    }

    // ESTE Evento CLASSIFICA PARA AQUELE Evento
    public function classifica()
    {
        return $this->hasOne("App\Evento", "evento_classificador_id", "id");
    }

    // AQUELE Grupo de Evento CLASSIFICA PARA ESTE Evento
    public function grupo_evento_classificador()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_classificador_id", "id");
    }

    public function grupo_evento()
    {
        return $this->belongsTo("App\GrupoEvento", "grupo_evento_id", "id");
    }

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }

    public function categorias_cadastradas()
    {
        return $this->hasMany("App\Categoria", "evento_id", "id");
    }

    public function categorias()
    {
        return $this->hasMany("App\CategoriaEvento", "evento_id", "id");
    }

    public function torneios()
    {
        return $this->hasMany("App\Torneio", "evento_id", "id");
    }

    public function criterios()
    {
        return $this->hasMany("App\CriterioDesempateEvento", "evento_id", "id");
    }

    public function tipo_rating_interno()
    {
        return $this->hasOne("App\TipoRatingEvento", "evento_id", "id");
    }

    public function pagina()
    {
        return $this->hasOne("App\Pagina", "evento_id", "id");
    }

    public function campos_adicionais()
    {
        return $this->hasMany("App\CampoPersonalizado", "evento_id", "id");
    }

    public function event_classificators()
    {
        return $this->hasMany("App\Classification\EventClassificate", "event_id", "id");
    }
    public function event_classificates()
    {
        return $this->hasMany("App\Classification\EventClassificate", "event_classificator_id", "id");
    }

    public function event_classificate_rules()
    {
        return $this->hasMany("App\Classification\EventClassificateRule", "event_id", "id");
    }

    public function tipo_rating()
    {
        if ($this->tipo_rating_interno()->count() > 0) {
            return $this->tipo_rating_interno();
        } else {
            return $this->grupo_evento->tipo_rating();
        }
    }

    public function email_templates()
    {
        return $this->hasMany("App\EmailTemplate", "evento_id", "id");
    }

    public function timeline_items()
    {
        return $this->hasMany("App\EventTimelineItem", "event_id", "id");
    }

    public function campos($private = [false], $where = [])
    {
        // if($this->hasMany("App\CampoPersonalizadoEvento","evento_id","id")->count() > 0){
        //     return $this->hasMany("App\CampoPersonalizadoEvento","evento_id","id");
        // }
        // return $this->grupo_evento->campos();
        $evento = $this;
        return CampoPersonalizado::where(function($q1) use ($evento){
                $q1->where([["grupo_evento_id", "=", $evento->grupo_evento->id]])
                ->orWhere([["evento_id", "=", $evento->id]]);
            })
            ->where(function($q1) use ($where){
                if(is_array($where)){
                    if(count($where) > 0){
                        $q1->where($where);
                    }
                }
            })
            ->whereIn("is_private", $private)
            ->get();
    }

    public function campos_obrigatorios($private = [false], $where = [])
    {
        // if($this->hasMany("App\CampoPersonalizadoEvento","evento_id","id")->count() > 0){
        //     return $this->hasMany("App\CampoPersonalizadoEvento","evento_id","id");
        // }
        // return $this->grupo_evento->campos();
        $evento = $this;
        return CampoPersonalizado::where(function($q1) use ($evento){
            $q1->where([["grupo_evento_id", "=", $evento->grupo_evento->id]])
            ->orWhere([["evento_id", "=", $evento->id]]);
        })
        ->where([["is_required","=",true]])
        ->whereIn("is_private",$private)
        ->where(function ($q1) use ($where) {
            if (is_array($where)) {
                if (count($where) > 0) {
                    $q1->where($where);
                }
            }
        })
        ->get();
    }


    public function configs(){
        return $this->hasMany("App\EventConfig","evento_id","id");
    }
    public function event_team_awards(){
        return $this->hasMany("App\EventTeamAward","events_id","id");
    }

    public function getTimelineItems(){
        $items = [];

        if ($this->hasConfig("date_start_registration",true)) {
            $items[] = [
                "datetime" => $this->getDataInicioInscricoesOnline(),
                "text" => "Início das Inscrições Online",
                "is_expected" => false
            ];
        }
        if ($this->data_limite_inscricoes_abertas) {
            $items[] = [
                "datetime" => $this->getDataFimInscricoesOnline(),
                "text" => "Fim das Inscrições Online",
                "is_expected" => false
            ];
        }

        foreach($this->timeline_items()->orderBy("order","ASC")->get() as $timeline_item){
            $item = [];

            $item["datetime"] = $timeline_item->getDateTime();
            $item["text"] = $timeline_item->title;
            $item["is_expected"] = $timeline_item->is_expected;


            $items[] = $item;
        }

        return $items;
    }

    public function getDataInicio()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_inicio);
        if ($datetime) {
            return $datetime->format("d/m/Y");
        } else {
            return false;
        }

    }

    public function getDataFim()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_fim);
        if ($datetime) {
            return $datetime->format("d/m/Y");
        } else {
            return false;
        }

    }

    public function getDataInicioInscricoesOnline()
    {
        if($this->hasConfig("date_start_registration")){
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->getConfig("date_start_registration", true));
            if ($datetime) {
                return $datetime->format("d/m/Y H:i");
            } else {
                return false;
            }
        }
        return false;
    }
    public function getDataFimInscricoesOnline()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i");
        } else {
            return false;
        }
    }

    public function getUrlName(){
        /* Remove pontos e underlines */
        $arrFindPointsUnderlines = array(".", "_");
        $arrSubstituir = '';
        $strTitulo = str_replace( $arrFindPointsUnderlines, $arrSubstituir, $this->name );


        /* Caracteres minúsculos */
        $strTitulo = strtolower($strTitulo);


        /* Remove os acentos */
        $acentos = array("á", "Á", "ã", "Ã", "â", "Â", "à", "À", "é", "É", "ê", "Ê", "è", "È", "í", "Í", "ó", "Ó", "õ", "Õ", "ò", "Ò", "ô", "Ô", "ú", "Ú", "ù", "Ù", "û", "Û", "ç", "Ç", "º", "ª");
        $letras = array("a", "A", "a", "A", "a", "A", "a", "A", "e", "E", "e", "E", "e", "E", "i", "I", "o", "O", "o", "O", "o", "O", "o", "O", "u", "U", "u", "U", "u", "U", "c", "C", "o", "a");

        $strTitulo = str_replace( $acentos, $letras, $strTitulo );
        $strTitulo = preg_replace( "/[^a-zA-Z0-9._$, ]/", "", $strTitulo );
        $strTitulo = iconv( "UTF-8", "UTF-8//TRANSLIT", $strTitulo );


        /* Remove espaços em branco */
        $strTitulo = str_replace( " ", "-", $strTitulo );
        $strTitulo = str_replace( "--", "-", $strTitulo );


        return $strTitulo;
    }

    public function isPaid(){
        if(
            env("XADREZSUICOPAG_URI",null) &&
            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
            env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
            $this->xadrezsuicopag_uuid != ""
        ){
            return true;
        }

        return false;
    }

    public function getEventPublicLink(){
        if($this->layout_version == 2){
            return url("/event/".$this->uuid."/".$this->getUrlName());
        }
        return url("/inscricao/".$this->id);
    }

    public function getCriterios(){
        if($this->criterios()->count() == 0){
            return $this->grupo_evento->criterios->all();
        }
        return $this->criterios->all();
    }


    public function ja_abriu_as_inscricoes()
    {
        if($this->hasConfig("date_start_registration")){
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->getConfig("date_start_registration",true));
            if($datetime->format("U") > time()){
                return false;
            }
        }
        return true;
    }

    public function inscricoes_encerradas($api = false, $only_date = false)
    {
        if($this->is_inscricoes_bloqueadas){
            return true;
        }
        if(!$this->ja_abriu_as_inscricoes() && !$only_date) {
            return true;
        }
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_limite_inscricoes_abertas);
        $datetime_data_fim = DateTime::createFromFormat('Y-m-d H:i:s', $this->data_fim." 23:59:59");
        if($datetime_data_fim){
            if ($datetime_data_fim->getTimestamp() <= time()) {
                return true;
            }
        }
        if(!$only_date && $this->estaLotado()){
            return true;
        }
        if ($datetime) {
            if($only_date){
                if ($api) {
                    if ($datetime->getTimestamp() + (60 * 5) >= time()) {
                        return false;
                    } else {
                        return true;
                    }
                }
                if ($datetime->getTimestamp() >= time()) {
                    return false;
                } else {
                    return true;
                }
            }

            if ($api) {
                if ($datetime->getTimestamp() + (60 * 5) >= time() && !$this->estaLotado()) {
                    return false;
                } else {
                    return true;
                }
            }
            if ($datetime->getTimestamp() >= time() && !$this->estaLotado()) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }

    }

    public function getRegraRating($enxadrista_id)
    {
        $evento = $this;
        $enxadrista = Enxadrista::find(Enxadrista::getStaticId($enxadrista_id));
        return TipoRatingRegras::where([
            ["tipo_ratings_id", "=", $evento->tipo_rating->tipo_ratings_id],
        ])
            ->where(function ($q1) use ($evento, $enxadrista) {
                $q1->where([
                    ["idade_minima", "<=", $enxadrista->howOld()],
                    ["idade_maxima", "=", null],
                ]);
                $q1->orWhere([
                    ["idade_minima", "=", null],
                    ["idade_maxima", ">=", $enxadrista->howOld()],
                ]);
                $q1->orWhere([
                    ["idade_minima", "<=", $enxadrista->howOld()],
                    ["idade_maxima", ">=", $enxadrista->howOld()],
                ]);
            })
            ->first();
    }

    public function quantosInscritos()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmados()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["confirmado", "=", true]])->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmadosWOs()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", true]])->count();
        }
        return $total;
    }
    public function quantosInscritosPresentes()
    {
        return $this->quantosInscritosConfirmados() - $this->quantosInscritosConfirmadosWOs();
    }
    public function quantosInscritoscomResultados()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["confirmado", "=", true]])->whereNotNull("pontos")->count();
        }
        return $total;
    }
    public function quantosInscritosConfirmadosLichess()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["is_lichess_found", "=", true]])->count();
        }
        return $total;
    }
    public function quantosInscritosFaltamLichess()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->count();
        }
        return $total - $this->quantosInscritosConfirmadosLichess();
    }
    public function estaLotado()
    {
        if ($this->maximo_inscricoes_evento) {
            if ($this->maximo_inscricoes_evento > 0) {
                if ($this->maximo_inscricoes_evento <= $this->quantosInscritos()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function enxadristaInscrito($enxadrista_id)
    {
        $total = 0;
        $evento = $this;
        $inscricao = Inscricao::where([
            ["enxadrista_id", "=", $enxadrista_id],
        ])
            ->whereHas("torneio", function ($q1) use ($evento) {
                $q1->where("evento_id", "=", $evento->id);
            })
            ->first();
        if ($inscricao) {
            return $inscricao;
        }

        return false;
    }
    public function inscritosPorClube($clube_id, $is_team = false)
    {
        $list = [];

        foreach ($this->torneios->all() as $torneio) {
            foreach ($torneio->inscricoes->all() as $inscricao) {
                if ($inscricao->clube->id == $clube_id) {
                    $list[($inscricao->categoria->id)][] = $inscricao;
                }
            }
        }

        if($is_team){
            foreach($list as $key => $list_categoria){
                usort($list_categoria, array("\App\Evento", "sort_team_lineup"));

                $list[$key] = $list_categoria;
            }
        }

        return $list;
    }

    public static function sort_team_lineup($ia,$ib)
    {
        if ($ia->hasConfig("team_order") && !$ib->hasConfig("team_order")) {
            return -1;
        }
        if (!$ia->hasConfig("team_order") && $ib->hasConfig("team_order")) {
            return 1;
        }
        if ($ia->hasConfig("team_order") && $ib->hasConfig("team_order")) {
            if ($ia->getConfig("team_order", true) == 0 && $ib->getConfig("team_order", true) > 0) {
                return 1;
            }
            if ($ia->getConfig("team_order", true) > 0 && $ib->getConfig("team_order", true) == 0) {
                return -1;
            }
            if ($ia->getConfig("team_order", true) > $ib->getConfig("team_order", true)) {
                return -1;
            }
            if ($ia->getConfig("team_order", true) < $ib->getConfig("team_order", true)) {
                return 1;
            }
        }
        return -1;
    }

    public function clubesInscritos(){
        $list = [];

        foreach($this->torneios->all() as $torneio){
            foreach($torneio->inscricoes->all() as $inscricao){
                if($inscricao->clube){
                    $list[($inscricao->clube->id)] = $inscricao->clube;
                }
            }
        }

        return $list;
    }

    public function getInscricoes()
    {
        $evento = $this;

        $inscricoes_id = Inscricao::whereHas("torneio", function ($q1) use ($evento) {
            $q1->where([["evento_id", "=", $evento->id]]);
        })
        ->join('enxadrista', 'enxadrista.id', '=', 'inscricao.enxadrista_id')
        ->orderBy("categoria_id", "ASC")
        ->orderBy("enxadrista.name", "ASC")
        ->pluck("inscricao.id");

        if(count($inscricoes_id->toArray()) == 0) return [];

        return Inscricao::whereIn("id",$inscricoes_id)->orderByRaw(DB::raw("FIELD(id, ".implode(",",$inscricoes_id->toArray()).")"))->get();
    }

    public function getInscricoesCacheadas($key){
        $list = array();

        foreach($this->getInscricoes() as $inscricao){
            $list[] = $inscricao->getCache($key);
        }

        return $list;
    }

    public function gerarToken()
    {
        if ($this->token == null) {
            $this->token = hash("sha1", "xadrezSuico" . time() . $this->id . $this->created_at . rand(0, 2345) . rand(25, rand(852, 658714)));
        }
    }

    public function inscricaoLiberada($token)
    {
        if ($this->e_inscricao_apenas_com_link) {
            if ($this->token == $token) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function getTorneioByCategoria($categoria_id){
        $torneio = $this->torneios->whereHas("categorias",function($q1) use ($categoria_id){
            $q1->where([
                ["categorias_id","=",$categoria_id]
            ]);
        })->first();
        if($torneio){
            return $torneio;
        }
        return false;
    }

    public function getLichessTeamLink(){
        if($this->lichess_team_id){
            return "http://lichess.org/team/".$this->lichess_team_id;
        }
        return "-";
    }
    public function getLichessTournamentLink(){
        if($this->lichess_tournament_id){
            return "http://lichess.org/swiss/".$this->lichess_tournament_id;
        }
        return "-";
    }

    public function isLichessDelayToEnter(){
        return true;
        if(strtotime($this->data_limite_inscricoes_abertas) <= time() - (60*60*12)){
            return false;
        }
        return true;
    }

    public function hasTorneiosEmparceiradosByXadrezSuico(){
        foreach($this->torneios->all() as $torneio){
            if($torneio->rodadas()->count() > 0){
                return true;
            }
        }
        return false;
    }

    public function getYear(){
        $datetime = DateTime::createFromFormat('Y-m-d', $this->data_inicio);
        if ($datetime) {
            return $datetime->format("Y");
        } else {
            return false;
        }
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if ($this->categorias()->count() > 0 || $this->categorias_cadastradas()->count() > 0 || $this->torneios()->count() > 0 || $this->criterios()->count() > 0 || $this->campos_adicionais()->count() > 0 || $this->classificador()->count() > 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function consegueCalcularRating(){
        $retorno = false;

        foreach($this->torneios->all() as $torneio){
            if($torneio->rodadas()->count() > 0){
                $retorno = true;
            }
        }

        return $retorno;
    }

    public function consegueCalcularClassificacaoGeral(){
        $retorno = true;

        foreach($this->torneios->all() as $torneio){
            if(!$torneio->getIsResultadosImportados()){
                $retorno = false;
            }
        }

        return $retorno;
    }


    public static function countAllReceivingRegister(){
        return count(Evento::getAllReceivingRegister());
    }
    public static function getAllReceivingRegister(){
        $events = array();
        foreach(Evento::all() as $evento){
            if(!$evento->inscricoes_encerradas()) $events[] = $evento;
        }
        return $events;
    }



    public function getConfirmacoesDataInicial()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->confirmacao_publica_inicio);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i");
        } else {
            return false;
        }
    }
    public function getConfirmacoesDataFim()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->confirmacao_publica_final);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i");
        } else {
            return false;
        }
    }
    public function setConfirmacoesDataInicial($data)
    {
        $datetime = DateTime::createFromFormat('d/m/Y H:i', $data);
        if ($datetime) {
            $this->confirmacao_publica_inicio = $datetime->format("Y-m-d H:i:s");
        } else {
            $this->confirmacao_publica_inicio = null;
        }
    }
    public function setConfirmacoesDataFim($data)
    {
        $datetime = DateTime::createFromFormat('d/m/Y H:i', $data);
        if ($datetime) {
            $this->confirmacao_publica_final = $datetime->format("Y-m-d H:i:s");
        } else {
            $this->confirmacao_publica_final = null;
        }
    }


    public function estaRecebendoConfirmacaoPublica(){
        if($this->e_permite_confirmacao_publica){
            if($this->confirmacao_publica_inicio && $this->confirmacao_publica_final){
                if($this->confirmacao_publica_inicio <= date("Y-m-d H:i:s") && $this->confirmacao_publica_final >= date("Y-m-d H:i:s")){
                    return true;
                }
            }
        }
        return false;
    }


    public function getTimeControl(){
        switch($this->tipo_modalidade){
            case 0:
                return "Convencional";
            case 1:
                return "Rápido";
            case 2:
                return "Relâmpago";
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
        $obj["date_start"] = $this->getDataInicio();
        $obj["date_finish"] = $this->getDataFim();
        $obj["place"] = $this->local;
        switch($this->tipo_modalidade){
            case 0:
                $obj["time_control"] = "STD";
                break;
            case 1:
                $obj["time_control"] = "RPD";
                break;
            case 2:
                $obj["time_control"] = "BTZ";
        }

        $obj["tournaments"] = array();
        foreach($this->torneios->all() as $torneio){
            $obj["tournaments"][] = ($send_data) ? $torneio->export("xadrezsuico-data") : $torneio->export("xadrezsuico");
        }

        return $obj;
    }

    public function generateUuid(){
        if($this->uuid == NULL){
            $this->uuid = Str::uuid();
            $this->save();
        }
    }

    public function getPublicCustomFields(){
        $evento = $this;
        return CampoPersonalizado::
            where(function($q1) use ($evento){
                $q1->where([["grupo_evento_id", "=", $evento->grupo_evento->id]])
                   ->orWhere([["evento_id", "=", $evento->id]]);
            })
            ->where([["is_public","=",true]])
            ->get();
    }

    public function getAPILimits(){
        return [
            "total" => $this->quantosInscritos(),
            "limit" => $this->maximo_inscricoes_evento
        ];
    }

    public function hasLimits(){
        return ($this->maximo_inscricoes_evento);
    }

    public function howManyPaid(){
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", true]])->count();
        }
        return $total;
    }
    public function howManyFree(){
        $total = 0;

        $categorias_id = $this->categorias()->whereNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->whereIn("categoria_id",$categorias_id)->count();
        }
        return $total;
    }
    public function howManyNotPaid(){
        $total = 0;
        $categorias_id = $this->categorias()->whereNotNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", false]])->whereIn("categoria_id",$categorias_id)->count();
        }
        return $total;
    }
    public function howManyConfirmedPaid()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", true],["confirmado","=",true]])->count();
        }
        return $total;
    }
    public function howManyConfirmedFree()
    {
        $total = 0;

        $categorias_id = $this->categorias()->whereNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->whereIn("categoria_id", $categorias_id)->where([["confirmado", "=", true]])->count();
        }
        return $total;
    }
    public function howManyConfirmedNotPaid()
    {
        $total = 0;
        $categorias_id = $this->categorias()->whereNotNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", false],["confirmado", "=", true]])->whereIn("categoria_id", $categorias_id)->count();
        }
        return $total;
    }

    public function howManyPresentPaid()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", true], ["confirmado", "=", true],["desconsiderar_pontuacao_geral", "=", false]])->count();
        }
        return $total;
    }
    public function howManyPresentFree()
    {
        $total = 0;

        $categorias_id = $this->categorias()->whereNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->whereIn("categoria_id", $categorias_id)->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", false]])->count();
        }
        return $total;
    }
    public function howManyPresentNotPaid()
    {
        $total = 0;
        $categorias_id = $this->categorias()->whereNotNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", false], ["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", false]])->whereIn("categoria_id", $categorias_id)->count();
        }
        return $total;
    }
    public function howManyWithResultsPaid()
    {
        $total = 0;
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", true], ["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", false]])->count();
        }
        return $total;
    }
    public function howManyWithResultsFree()
    {
        $total = 0;

        $categorias_id = $this->categorias()->whereNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();

        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->whereIn("categoria_id", $categorias_id)->where([["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", false]])->count();
        }
        return $total;
    }
    public function howManyWithResultsNotPaid()
    {
        $total = 0;
        $categorias_id = $this->categorias()->whereNotNull("xadrezsuicopag_uuid")->pluck("categoria_id")->toArray();
        foreach ($this->torneios->all() as $torneio) {
            $total += $torneio->inscricoes()->where([["paid", "=", false], ["confirmado", "=", true], ["desconsiderar_pontuacao_geral", "=", false]])->whereIn("categoria_id", $categorias_id)->count();
        }
        return $total;
    }

    public function isEvent(){
        return true;
    }

    public function isEventGroup(){
        return false;
    }

    public function getTournamentWithMoreRegistrations()
    {
        $total = 0;
        $tournament = ["status"=>true,"tournament"=>null,"total"=>0];

        foreach($this->torneios->all() as $torneio){
            if($torneio->inscricoes()->count() > $tournament["total"]){
                $tournament["tournament"] = $torneio;
                $tournament["total"] = $torneio->inscricoes()->count();
            }
        }
        if(!$tournament["tournament"]){
            $tournament["status"] = false;
            $tournament["tournament"] = "-- Sem Torneios --";
        }

        return $tournament;
    }

    public function classificator_getCategories(){
        $categories = array();
        foreach($this->event_classificates->all() as $event_classificate){
            foreach($event_classificate->event->categorias->all() as $categoria_relacionada){
                $categories[] = $categoria_relacionada->categoria;
            }
        }
        return $categories;
    }

    public function hasCategoryForEnxadrista($enxadrista)
    {
        $evento = $this;
        return $this->categorias()->whereHas("categoria", function ($q1) use ($enxadrista, $evento) {
            $q1->where(function ($q2) use ($enxadrista, $evento) {
                $q2->where(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                })
                    ->orWhere(function ($q3) use ($enxadrista, $evento) {
                        $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                        $q3->where([["idade_maxima", "=", null]]);
                    })
                    ->orWhere(function ($q3) use ($enxadrista, $evento) {
                        $q3->where([["idade_minima", "=", null]]);
                        $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    })
                    ->orWhere(function ($q3) {
                        $q3->where([["idade_minima", "=", null]]);
                        $q3->where([["idade_maxima", "=", null]]);
                    });
            })
            ->where(function ($q2) use ($enxadrista) {
                $q2->where(function ($q3) use ($enxadrista) {
                    if ($enxadrista->sexos_id) {
                        $q3->where(function ($q4) use ($enxadrista) {
                            $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                            });
                        });
                        $q3->orWhere(function ($q4) {
                            $q4->whereDoesntHave("sexos");
                        });
                    } else {
                        $q3->whereDoesntHave("sexos");
                    }
                });
            });
        })
        ->count() > 0;
    }
    public function getCategoryForEnxadrista($enxadrista)
    {
        $evento = $this;
        return $this->categorias()->whereHas("categoria", function ($q1) use ($enxadrista, $evento) {
            $q1->where(function ($q2) use ($enxadrista, $evento) {
                $q2->where(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                })
                ->orWhere(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    $q3->where([["idade_maxima", "=", null]]);
                })
                ->orWhere(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "=", null]]);
                    $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                })
                ->orWhere(function ($q3) {
                    $q3->where([["idade_minima", "=", null]]);
                    $q3->where([["idade_maxima", "=", null]]);
                });
            })
            ->where(function ($q2) use ($enxadrista) {
                $q2->where(function ($q3) use ($enxadrista) {
                    if ($enxadrista->sexos_id) {
                        $q3->where(function ($q4) use ($enxadrista) {
                            $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                            });
                        });
                        $q3->orWhere(function ($q4) {
                            $q4->whereDoesntHave("sexos");
                        });
                    } else {
                        $q3->whereDoesntHave("sexos");
                    }
                });
            });
        })
        ->first();
    }
    public function getCategoriesForEnxadrista($enxadrista, $is_user_with_permission = false)
    {
        $evento = $this;
        return $this->categorias()->whereHas("categoria", function ($q1) use ($enxadrista, $evento, $is_user_with_permission) {
            $q1->where(function ($q2) use ($enxadrista, $evento) {
                $q2->where(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                })
                ->orWhere(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "<=", $enxadrista->howOldForEvento($evento->getYear())]]);
                    $q3->where([["idade_maxima", "=", null]]);
                })
                ->orWhere(function ($q3) use ($enxadrista, $evento) {
                    $q3->where([["idade_minima", "=", null]]);
                    $q3->where([["idade_maxima", ">=", $enxadrista->howOldForEvento($evento->getYear())]]);
                })
                ->orWhere(function ($q3) {
                    $q3->where([["idade_minima", "=", null]]);
                    $q3->where([["idade_maxima", "=", null]]);
                });
            })
            ->where(function ($q2) use ($enxadrista) {
                $q2->where(function ($q3) use ($enxadrista) {
                    if ($enxadrista->sexos_id) {
                        $q3->where(function ($q4) use ($enxadrista) {
                            $q4->whereHas("sexos", function ($q5) use ($enxadrista) {
                                $q5->where([["sexos_id", "=", $enxadrista->sexos_id]]);
                            });
                        });
                        $q3->orWhere(function ($q4) {
                            $q4->whereDoesntHave("sexos");
                        });
                    } else {
                        $q3->whereDoesntHave("sexos");
                    }
                });
            });
            if (!$is_user_with_permission) {
                $q1->where([["is_private", "=", false]]);
            }
        })
        ->get();
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
                $event_config = $this->configs()->where([["key","=",$key]])->first();
                switch($event_config->value_type){
                    case ConfigType::Integer:
                        return $event_config->integer;
                    case ConfigType::Float:
                        return $event_config->float;
                    case ConfigType::Decimal:
                        return $event_config->decimal;
                    case ConfigType::Boolean:
                        return $event_config->boolean;
                    case ConfigType::String:
                        return $event_config->string;
                    case ConfigType::Date:
                        return $event_config->date;
                    case ConfigType::DateTime:
                        return $event_config->datetime;
                }
            }

            return ["ok"=>1,"error"=>0,"config"=>$this->configs()->where([["key","=",$key]])->first()];
        }
        if($return_value) return null;

        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }
    public function removeConfig($key){
        if($this->hasConfig($key)){
            $event_config = $this->configs()->where([["key","=",$key]])->first();

            $event_config->delete();

            return ["ok"=>1,"error"=>0];
        }
        return ["ok"=>0,"error"=>1,"message"=>"Configuração não encontrada."];
    }

    public function setConfig($key,$type,$value){
        if($this->hasConfig($key)){
            $event_config = $this->configs()->where([["key","=",$key]])->first();

            if($event_config->value_type != $type){
                return ["ok"=>0,"error"=>1,"message"=>"O tipo do campo é diferente - ".$event_config->value_type." != ".$type];
            }
        }else{
            $event_config = new EventConfig;
            $event_config->evento_id = $this->id;
            $event_config->key = $key;
            $event_config->value_type = $type;
        }

        switch($type){
            case ConfigType::Integer:
                $event_config->integer = $value;
                break;
            case ConfigType::Float:
                $event_config->float = $value;
                break;
            case ConfigType::Decimal:
                $event_config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $event_config->boolean = $value;
                break;
            case ConfigType::String:
                $event_config->string = $value;
                break;
            case ConfigType::Date:
                $event_config->date = $value;
                break;
            case ConfigType::DateTime:
                $event_config->datetime = $value;
                break;
        }

        $event_config->save();

        return ["ok"=>1,"error"=>0];
    }

    public function getCacheKey($type = "registration_public_list"){
        return "event_" . ($this->uuid ? $this->uuid : $this->id) . "_".$type;
    }


    public function toAPIObject(){
        return [
            "id" => $this->id,
            "uuid" => $this->uuid,
            "name" => $this->name,
            "xadrezsuicopag_uuid" => $this->xadrezsuicopag_uuid,
        ];
    }
}
