<?php

namespace App;

use App\Enum\ConfigType;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Util\Util;

use App\Rating;
use App\MovimentacaoRating;


use Log;
use PDO;

class Enxadrista extends Model
{
    use LogsActivity;

    protected $fillable = ['*'];

    protected static $logFillable = true;

    protected static $logAttributes = ['*'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'enxadrista';

    public function cidade()
    {
        return $this->belongsTo("App\Cidade", "cidade_id", "id");
    }
    public function clube()
    {
        return $this->belongsTo("App\Clube", "clube_id", "id");
    }
    public function sexo()
    {
        return $this->belongsTo("App\Sexo", "sexos_id", "id");
    }
    public function pais_nascimento()
    {
        return $this->belongsTo("App\Pais", "pais_id", "id");
    }
    public function pais_celular()
    {
        return $this->belongsTo("App\Pais", "pais_celular_id", "id");
    }
    public function documentos()
    {
        return $this->hasMany("App\Documento", "enxadrista_id", "id");
    }
    public function inscricoes()
    {
        return $this->hasMany("App\Inscricao", "enxadrista_id", "id");
    }
    public function ratings()
    {
        return $this->hasMany("App\Rating", "enxadrista_id", "id");
    }
    public function pontuacoes_gerais()
    {
        return $this->hasMany("App\PontuacaoEnxadrista", "enxadrista_id", "id");
    }
    public function criterios_desempate_gerais()
    {
        return $this->hasMany("App\EnxadristaCriterioDesempateGeral", "enxadrista_id", "id");
    }
    public function vinculos()
    {
        return $this->hasMany("App\Vinculo", "enxadrista_id", "id");
    }
    public function titles()
    {
        return $this->hasMany("App\PlayerTitle", "players_id", "id");
    }

    public function configs()
    {
        return $this->hasMany("App\PlayerConfig", "enxadrista_id", "id");
    }

    // Method to get the correct ID
    public function getId(){
        if($this->hasConfig("united_to")){
            return Enxadrista::find($this->getConfig("united_to",true))->getId();
        }
        return $this->id;
    }

    public static function getStaticId($id){
        if(Enxadrista::where([["id","=",$id]])->count()){
            $enxadrista = Enxadrista::find($id);

            return $enxadrista->getId();
        }
        return 0;
    }

    public function getTitle(){
        // FIDE
        if ($this->titles()->whereHas("title", function ($q1) {
            $q1->where([["entities_id", "=", 1]]);
        })->count()) {
            return $this->titles()->whereHas("title", function ($q1) {
                $q1->where([["entities_id", "=", 1]]);
            })->first();
        }
        // CBX
        if ($this->titles()->whereHas("title", function ($q1) {
            $q1->where([["entities_id", "=", 2]]);
        })->count()) {
            return $this->titles()->whereHas("title", function ($q1) {
                $q1->where([["entities_id", "=", 2]]);
            })->first();
        }

        return null;
    }

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->inscricoes()->count() > 0 ||
                $this->ratings()->count() > 0 ||
                $this->hasConfig("united_to")
            ) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getName()
    {
        return mb_strtoupper($this->name);
    }
    public function gerNameSemCaracteresEspeciais(){
        return $this->getNameSemCaracteresEspeciais();
    }
    public function getNameSemCaracteresEspeciais()
    {
        $str = mb_strtolower($this->name);
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        return mb_strtoupper($str);
    }

    public function hasDocument($document_types_id, $document = null){
        if($document == null){
            return $this->documentos()->where([["tipo_documentos_id", "=", $document_types_id]])->count() > 0;
        }
        if($document_types_id == 1){
            $document = Util::numeros($document);
        }
        return $this->documentos()->where([["tipo_documentos_id","=",$document_types_id],["numero","=",$document]])->count() > 0;
    }

    /*
     * Esta função serve para dividir o nome completo do enxadrista para os campos de Nome e Sobrenome que são usados
     * pela CBX, FIDE e LBX.
     */
    public function splitName(){
        if($this->name){
            // Esta função divide os nome em uma lista dividida pelo espaço...
            $explode_name = explode(" ",$this->getNameSemCaracteresEspeciais());

            // Conta a quantidade de itens que existem na lista do nome divida pelo espaço
            $total = count($explode_name);

            $last_name_items = array();

            $explode_name_inverse = array_reverse($explode_name);

            // Se o número de nomes for 2, então o último coloca no lastname e o primeiro no firstname
            if($total == 2){
                $this->firstname = mb_convert_case($explode_name[0],MB_CASE_TITLE,"UTF-8");
                $this->lastname = mb_convert_case($explode_name[1],MB_CASE_TITLE,"UTF-8");
            }else{
                $first_name = "";
                $last_name = "";

                $found_last_name = false;
                $finished_last_name = false;
                $i = 1;
                foreach($explode_name_inverse as $this_name){
                    if($i == 1){
                        $last_name_items[] = ($total - $i);
                        $last_name = $this_name;
                        if(!Util::eGeracaoDeFamilia($this_name)){
                            $found_last_name = true;
                        }
                    }else{
                        if(!$finished_last_name){
                            if($found_last_name){
                                if(Util::ePreposicao($this_name)){
                                    $last_name = $this_name." ".$last_name;
                                    $finished_last_name = true;
                                }else{
                                    if($first_name == ""){
                                        $first_name = $this_name;
                                    }else{
                                        $first_name = $this_name." ".$first_name;
                                    }
                                    $finished_last_name = true;
                                }
                            }else{
                                if(!Util::eGeracaoDeFamilia($this_name)){
                                    $last_name = $this_name." ".$last_name;
                                    $found_last_name = true;
                                }elseif(Util::ePreposicao($this_name)){
                                    $last_name = $this_name." ".$last_name;
                                    $finished_last_name = true;
                                }else{
                                    if($first_name == ""){
                                        $first_name = $this_name;
                                    }else{
                                        $first_name = $this_name." ".$first_name;
                                    }
                                    $finished_last_name = true;
                                }
                            }
                        }else{
                            $first_name = $this_name." ".$first_name;
                        }
                    }
                    $i++;
                }
                $this->firstname = mb_convert_case($first_name,MB_CASE_TITLE,"UTF-8");
                $this->lastname = mb_convert_case($last_name,MB_CASE_TITLE,"UTF-8");
                // echo $this->id."-".$this->firstname. " ". $this->lastname."<br/>";
            }
        }
    }

    public function setBorn($born)
    {
        $datetime = DateTime::createFromFormat('d/m/Y', $born);
        if ($datetime) {
            $this->born = $datetime->format("Y-m-d");
            return true;
        } else {
            return false;
        }

    }
    public function getBorn()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if ($datetime) {
            return $datetime->format("d/m/Y");
        } else {
            return false;
        }

    }
    public function getBornLGPD()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if ($datetime) {
            return "**/**/".$datetime->format("Y");
        } else {
            return false;
        }

    }
    public function setBornFromSM($born)
    {
        $datetime = DateTime::createFromFormat('d.m.Y', $born);
        if ($datetime) {
            $this->born = $datetime->format("Y-m-d");
            return true;
        } else {
            return false;
        }

    }
    public function getBornToSM()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if ($datetime) {
            return $datetime->format("d.m.Y");
        } else {
            return false;
        }

    }

    public function howOld()
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if ($datetime) {
            return date("Y") - $datetime->format("Y");
        } else {
            return false;
        }

    }

    public function howOldForEvento($year)
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $this->born);
        if ($datetime) {
            return $year - $datetime->format("Y");
        } else {
            return false;
        }

    }

    public function estaInscrito($evento_id)
    {
        $enxadrista = $this;
        if ($this->whereHas("inscricoes", function ($q1) use ($evento_id, $enxadrista) {
            $q1->where([["enxadrista_id", "=", $enxadrista->id]]);
            $q1->whereHas("torneio", function ($q2) use ($evento_id, $enxadrista) {
                $q2->where([["evento_id", "=", $evento_id]]);
            });
        })->count() > 0) {
            return true;
        }
        return false;
    }


    public function getInscricao($evento_id)
    {
        if ($this->estaInscrito($evento_id) > 0) {
            $inscricao = $this->inscricoes()->whereHas("torneio", function ($q2) use ($evento_id) {
                $q2->where([["evento_id", "=", $evento_id]]);
            })->first();
            return $inscricao;
        }
        return false;
    }

    public function ratingParaEvento($evento_id,$gera_senao_houver = false)
    {
        $enxadrista = $this;
        $evento = Evento::find($evento_id);
        if ($evento) {
            if ($evento->tipo_rating) {
                $rating = $this->ratings()->where([["tipo_ratings_id", "=", $evento->tipo_rating->tipo_ratings_id]])->first();
                if ($rating) {
                    if ($rating->valor > 0) {
                        return $rating->valor;
                    }
                }
                $rating_regra = TipoRatingRegras::where([
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

                $rating_inicial = $rating_regra->inicial;

                $fide = $enxadrista->showRating(0, $evento->tipo_modalidade);
                $cbx = $enxadrista->showRating(1, $evento->tipo_modalidade);
                $lbx = $enxadrista->showRating(2, $evento->tipo_modalidade);

                $found = false;
                if($fide){
                    if($fide > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($enxadrista->howOld(), $evento)){
                        $rating_inicial = $fide;
                        $found = true;
                    }
                }
                if($lbx && !$found){
                    if($lbx > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($enxadrista->howOld(), $evento)){
                        $rating_inicial =  $lbx;
                        $found = true;
                    }
                }
                if($cbx && !$found){
                    if($cbx > $evento->tipo_rating->tipo_rating->showRatingRegraIdade($enxadrista->howOld(), $evento)){
                        $rating_inicial = $cbx;
                        $found = true;
                    }
                }

                // Log::debug("Idade: ".$enxadrista->howOld());
                if($gera_senao_houver){
                    $rating = new Rating;
                    $rating->tipo_ratings_id = $evento->tipo_rating->tipo_rating->id;
                    $rating->enxadrista_id = $enxadrista->id;
                    $rating->valor = $rating_inicial;
                    $rating->save();

                    $movimentacao = new MovimentacaoRating;
                    $movimentacao->tipo_ratings_id = $rating->tipo_ratings_id;
                    $movimentacao->ratings_id = $rating->id;
                    $movimentacao->valor = $rating_inicial;
                    $movimentacao->is_inicial = true;
                    $movimentacao->save();
                }

                return $rating_inicial;
            } else {
                if ($evento->usa_fide) {
                    if($this->showRating(0, $evento->tipo_modalidade)) return $this->showRating(0, $evento->tipo_modalidade);
                } elseif ($evento->usa_lbx) {
                    if($this->showRating(2, $evento->tipo_modalidade)) return $this->showRating(2, $evento->tipo_modalidade);
                }
                if ($evento->usa_cbx) {
                    if($this->showRating(1, $evento->tipo_modalidade)) return $this->showRating(1, $evento->tipo_modalidade);
                }
            }
        }
        return false;
    }
    public function hasRatingParaEvento($evento_id)
    {
        $enxadrista = $this;
        $evento = Evento::find($evento_id);
        if ($evento) {
            if ($evento->tipo_rating) {
                $rating = $this->ratings()->where([["tipo_ratings_id", "=", $evento->tipo_rating->tipo_ratings_id]])->first();
                if ($rating) {
                    if ($rating->valor > 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function KParaEvento($evento_id)
    {
        $enxadrista = $this;
        $evento = Evento::find($evento_id);
        if ($evento) {
            if ($evento->tipo_rating) {
                $regra_idade = $evento->tipo_rating->tipo_rating->regraIdade($enxadrista->howOld(), $evento);
                if($regra_idade){
                    return $regra_idade->k;
                }
            }
        }
        return 20;
    }


    public function temRating($evento_id = null, $tipo_ratings_id = null)
    {
        $enxadrista = $this;
        if($evento_id){
            $evento = Evento::find($evento_id);
            if ($evento->tipo_rating) {
                $rating_regra = TipoRatingRegras::where([
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
                $rating = $this->ratings()->where([["tipo_ratings_id", "=", $evento->tipo_rating->tipo_ratings_id]])->first();
                if ($rating) {
                    if ($rating->valor > 0) {
                        return ["ok" => 1, "rating" => $rating, "regra" => $rating_regra];
                    }
                    return ["ok" => 0, "rating" => $rating, "regra" => $rating_regra];
                }
            }
        }elseif($tipo_ratings_id){
            $tipo_rating = TipoRating::find($tipo_ratings_id);

            $rating_regra = TipoRatingRegras::where([
                ["tipo_ratings_id", "=", $tipo_rating->id],
            ])
                ->where(function ($q1) use ($enxadrista) {
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
            $rating = $this->ratings()->where([["tipo_ratings_id", "=", $tipo_rating->id]])->first();
            if ($rating) {
                if ($rating->valor > 0) {
                    return ["ok" => 1, "rating" => $rating, "regra" => $rating_regra];
                }
                return ["ok" => 0, "rating" => $rating, "regra" => $rating_regra];
            }else{
                return ["ok" => 0, "rating" => null, "regra" => $rating_regra];
            }
        }
        return false;
    }

    public static function getComInscricaoConfirmada($grupo_evento_id, $categoria_id)
    {
        return Enxadrista::whereHas("inscricoes", function ($q1) use ($grupo_evento_id, $categoria_id) {
            $q1->whereHas("torneio", function ($q2) use ($grupo_evento_id, $categoria_id) {
                $q2->whereHas("evento", function ($q3) use ($grupo_evento_id, $categoria_id) {
                    $q3->where([
                        ["grupo_evento_id", "=", $grupo_evento_id],
                    ]);
                });
            });
            $q1->where([
                ["categoria_id", "=", $categoria_id],
            ]);
        })
            ->get();
    }

    public static function getComPontuacaoGeral($grupo_evento_id, $categoria_id)
    {
        return Enxadrista::whereHas("pontuacoes_gerais", function ($q1) use ($grupo_evento_id, $categoria_id) {
            $q1->where([
                ["categoria_id", "=", $categoria_id],
                ["grupo_evento_id", "=", $grupo_evento_id],
            ]);
        })
            ->get();
    }

    public function getPontuacaoGeral($grupo_evento_id, $categoria_id)
    {
        return $this->pontuacoes_gerais()->where([
            ["categoria_id", "=", $categoria_id],
            ["grupo_evento_id", "=", $grupo_evento_id],
        ])->first();
    }

    public function getInscricoesByGrupoEventoECategoria($grupo_evento_id, $categoria_id)
    {
        return $this->inscricoes()->where([
            ["categoria_id", "=", $categoria_id],
        ])
            ->whereHas("torneio", function ($q1) use ($grupo_evento_id) {
                $q1->whereHas("evento", function ($q2) use ($grupo_evento_id) {
                    $q2->where([
                        ["grupo_evento_id", "=", $grupo_evento_id],
                        ["classificavel", "=", true],
                    ]);
                });
            })
            ->orderBy("torneio_id", "ASC")
            ->get();
    }

    public function getCategoriasParticipantesbyGrupoEvento($grupo_evento_id)
    {
        $inscricoes = $this->inscricoes()
            ->where([
                ["posicao", "!=", null],
            ])
            ->whereHas("torneio", function ($q1) use ($grupo_evento_id) {
                $q1->whereHas("evento", function ($q2) use ($grupo_evento_id) {
                    $q2->where([
                        ["grupo_evento_id", "=", $grupo_evento_id],
                        ["classificavel", "=", true],
                    ]);
                });
            })->get();
        $categorias = array();
        foreach ($inscricoes as $inscricao) {
            if (!in_array($inscricao->categoria_id, $categorias)) {
                $categorias[] = $inscricao->categoria_id;
            }
        }
        return Categoria::whereIn("id", $categorias)->get();
    }

    public function getNomePublico()
    {
        return $this->name;
    }

    public function getNascimentoPublico()
    {
        return $this->getBornLGPD();
    }

    public function getNomePrivado()
    {
        return $this->name;
    }

    public function getNascimentoPrivado()
    {
        return $this->getBorn();
    }

    public function getRating($entidade, $tipo_modalidade)
    {
        $rating = Rating::where([
            ["tipo_modalidade", "=", $tipo_modalidade],
            ["entidade", "=", $entidade],
            ["enxadrista_id", "=", $this->id],
        ])
            ->whereNull("tipo_ratings_id")
            ->first();
        if ($rating) {
            return $rating;
        }

        return false;
    }

    public function setRating($entidade, $tipo_modalidade, $valor)
    {
        $rating = $this->getRating($entidade, $tipo_modalidade);
        if (!$rating) {
            $rating = new Rating;
            $rating->enxadrista_id = $this->id;
            $rating->entidade = $entidade;
            $rating->tipo_modalidade = $tipo_modalidade;
        }
        $rating->valor = $valor;
        $rating->save();
    }

    public function showRating($entidade, $tipo_modalidade, $fide_sequence = false)
    {
        $rating = $this->getRating($entidade, $tipo_modalidade);
        if ($rating) {
            return $rating->valor;
        }

        if($fide_sequence){
            if($tipo_modalidade >= 1){
                $rating_std = $this->getRating($entidade, 0);
                if ($rating_std) {
                    return $rating_std->valor;
                }
            }
            if($tipo_modalidade == 2){
                $rating_rpd = $this->getRating($entidade, 1);
                if ($rating_rpd) {
                    return $rating_rpd->valor;
                }
            }
        }
        return false;
    }
    public function deleteRating($entidade, $tipo_modalidade)
    {
        $rating = $this->getRating($entidade, $tipo_modalidade);
        if ($rating) {
            $rating->delete();
        }
    }

    public function getNameToSM()
    {
        $nome = $this->getName();
        $explode = explode(" ", $nome);
        $count = count($explode);
        $i = 1;
        $retorno = "";
        foreach ($explode as $part_nome) {
            $retorno .= $part_nome;
            if ($i == ($count - 1)) {
                $retorno .= ",";
            } elseif ($i != $count) {
                $retorno .= " ";
            }
            $i++;
        }
        return $retorno;
    }
    public function getRatingInterno($tipo_ratings_id)
    {
        $rating = Rating::where([
            ["tipo_ratings_id", "=", $tipo_ratings_id],
            ["enxadrista_id", "=", $this->id],
        ])
            ->first();
        if ($rating) {
            return $rating;
        }

        return false;
    }

    public function showRatingInterno($tipo_ratings_id)
    {
        $rating = $this->getRatingInterno($tipo_ratings_id);
        if ($rating) {
            return $rating->valor;
        }
        return false;
    }

    public function setFIDEID($fide_id = null){
        $codigo_organizacao = 0;
        if($this->getOriginal("fide_id") != $fide_id || $this->fide_id != $fide_id){
            $this->fide_id = $fide_id;
            $this->fide_name = null;
            $this->fide_last_update = null;
            $this->encontrado_fide = false;

            $this->save();

            $this->deleteRating($codigo_organizacao,0);
            $this->deleteRating($codigo_organizacao,1);
            $this->deleteRating($codigo_organizacao,2);
        }
    }
    public function setCBXID($cbx_id = null){
        $codigo_organizacao = 1;
        if($this->getOriginal("cbx_id") != $cbx_id || $this->cbx_id != $cbx_id){
            $this->cbx_id = $cbx_id;
            $this->cbx_name = null;
            $this->cbx_last_update = null;
            $this->encontrado_cbx = false;

            $this->save();

            $this->deleteRating($codigo_organizacao,0);
            $this->deleteRating($codigo_organizacao,1);
            $this->deleteRating($codigo_organizacao,2);
        }
    }
    public function setLBXID($lbx_id = null){
        $codigo_organizacao = 2;
        if($this->getOriginal("lbx_id") != $lbx_id || $this->lbx_id != $lbx_id){
            $this->lbx_id = $lbx_id;
            $this->lbx_name = null;
            $this->lbx_last_update = null;
            $this->encontrado_lbx = false;

            $this->save();

            $this->deleteRating($codigo_organizacao,0);
            $this->deleteRating($codigo_organizacao,1);
            $this->deleteRating($codigo_organizacao,2);
        }
    }


    public function getInscricoesByClube($clube_id, $year = null, $is_confirmed = true){
        if(!$year){
            $year = date("Y");
        }
        if($is_confirmed){
            return $this->inscricoes()->where([
                ["clube_id","=",$clube_id],
                ["confirmado", "=", true],
                ["is_desclassificado", "=", false],
                ["desconsiderar_pontuacao_geral", "=", false],
                ["desconsiderar_classificado", "=", false],
            ])
            ->whereHas("torneio", function ($q2) use ($year) {
                $q2->whereHas("evento", function ($q3) use ($year) {
                    $q3->where([
                        ["data_inicio", ">=", $year . "-01-01"],
                        ["data_fim", "<=", $year . "-12-31"],
                    ])
                    ->where(function($q4){
                        $q4->where([["classificavel","=",true]]);
                        $q4->orWhere([["mostrar_resultados","=",true]]);
                    });
                });
            })
            ->get();
        }
        return $this->inscricoes()->where([["clube_id","=",$clube_id]])->get();
    }


    public function getClubePublico(){
        if($this->last_cadastral_update > "2022-01-01 00:00:00"){
            return "#".$this->clube->id." - ".$this->clube->getName();
        }else{
            if(env("ENTITY_DOMAIN",NULL) == "fexpar.com.br"){
                if($this->clube->is_fexpar___clube_filiado){
                    return "#".$this->clube->id." - ".$this->clube->getName();
                }
            }
            if($this->howOld() >= 18){
                return "#".$this->clube->id." - ".$this->clube->getName();
            }
            return "-- Clube Não Liberado para Esta Lista --";
        }
    }


    public function estaAptoParaPreVinculacao(){
        if(!$this->pais_id){
            return -7;
        }
        if($this->pais_id == 33){
            if($this->documentos()->where([
                ["tipo_documentos_id", "=", 1],
            ])->count() == 0){
                return -1;
            }
            if($this->documentos()->where([
                ["tipo_documentos_id", "=", 2],
            ])->count() == 0){
                return -2;
            }
        }else{
            if($this->documentos()->whereIn("tipo_documentos_id", [3,4])->count() == 0){
                return -3;
            }
        }
        if($this->cidade()->whereHas("estado", function ($q2) {
                $q2->where([
                    ["ibge_id", "=", 41],
                ]);
            })
            ->count() == 0){
            return -4;
        }
        if($this->clube()->whereHas("cidade", function ($q2) {
            $q2->whereHas("estado", function ($q3) {
                $q3->where([
                    ["ibge_id", "=", 41],
                ]);
            });
        })
        ->where([["is_fexpar___clube_valido_vinculo_federativo", "=", true]])
        ->count() == 0){
            return -5;
        }

        if($this->inscricoes()->where([
            ["confirmado", "=", true],
            ["is_desclassificado", "=", false],
            ["desconsiderar_pontuacao_geral", "=", false],
            ["desconsiderar_classificado", "=", false],
        ])
        ->whereHas("torneio", function ($q2) {
            $q2->whereHas("evento", function ($q3) {
                $q3->where([
                    ["data_inicio", ">=", date("Y") . "-01-01"],
                    ["data_fim", "<=", date("Y") . "-12-31"],
                ])
                ->where(function($q4){
                    $q4->where([["classificavel","=",true]]);
                    $q4->orWhere([["mostrar_resultados","=",true]]);
                });
            });
        })
        ->count() == 0){
            return -6;
        }

        return true;
    }




    public function getConfigs()
    {
        return $this->configs->all();
    }

    public function hasConfig($key)
    {
        if ($this->configs()->where([["key", "=", $key]])->count() > 0) {
            return true;
        }
        return false;
    }
    public function getConfig($key, $return_value = false)
    {
        if ($this->hasConfig($key)) {
            if ($return_value) {
                $config = $this->configs()->where([["key", "=", $key]])->first();
                switch ($config->value_type) {
                    case ConfigType::Integer:
                        return $config->integer;
                    case ConfigType::Float:
                        return $config->float;
                    case ConfigType::Decimal:
                        return $config->decimal;
                    case ConfigType::Boolean:
                        return $config->boolean;
                    case ConfigType::String:
                        return $config->string;
                    case ConfigType::Date:
                        return $config->date;
                    case ConfigType::DateTime:
                        return $config->datetime;
                }
            }

            return ["ok" => 1, "error" => 0, "config" => $this->configs()->where([["key", "=", $key]])->first()];
        }
        if ($return_value) return null;

        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }
    public function removeConfig($key)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            $config->delete();

            return ["ok" => 1, "error" => 0];
        }
        return ["ok" => 0, "error" => 1, "message" => "Configuração não encontrada."];
    }

    public function setConfig($key, $type, $value)
    {
        if ($this->hasConfig($key)) {
            $config = $this->configs()->where([["key", "=", $key]])->first();

            if ($config->value_type != $type) {
                return ["ok" => 0, "error" => 1, "message" => "O tipo do campo é diferente - " . $registration_config->value_type . " != " . $type];
            }
        } else {
            $config = new PlayerConfig;
            $config->enxadrista_id = $this->id;
            $config->key = $key;
            $config->value_type = $type;
        }

        switch ($type) {
            case ConfigType::Integer:
                $config->integer = $value;
                break;
            case ConfigType::Float:
                $config->float = $value;
                break;
            case ConfigType::Decimal:
                $config->decimal = $value;
                break;
            case ConfigType::Boolean:
                $config->boolean = $value;
                break;
            case ConfigType::String:
                $config->string = $value;
                break;
            case ConfigType::Date:
                $config->date = $value;
                break;
            case ConfigType::DateTime:
                $config->datetime = $value;
                break;
        }

        $config->save();

        return ["ok" => 1, "error" => 0];
    }
}
