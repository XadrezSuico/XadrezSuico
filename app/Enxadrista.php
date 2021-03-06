<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Util\Util;

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

    public function isDeletavel()
    {
        if ($this->id != null) {
            if (
                $this->inscricoes()->count() > 0 ||
                $this->ratings()->count() > 0
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

    public function ratingParaEvento($evento_id)
    {
        $enxadrista = $this;
        $evento = Evento::find($evento_id);
        if ($evento) {
            if ($evento->tipo_rating) {
                $inscricao = $this->whereHas("inscricoes", function ($q1) use ($evento_id, $enxadrista) {
                    $q1->where([["enxadrista_id", "=", $enxadrista->id]]);
                    $q1->whereHas("torneio", function ($q2) use ($evento_id, $enxadrista) {
                        $q2->where([["evento_id", "=", $evento_id]]);
                    });
                });
                if ($inscricao->count() > 0) {
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
                            return $rating->valor;
                        }
                    }
                    return $rating_regra->inicial;
                }
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

    public function temRating($evento_id)
    {
        $enxadrista = $this;
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
        return $this->getBorn();
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

    public function showRating($entidade, $tipo_modalidade)
    {
        $rating = $this->getRating($entidade, $tipo_modalidade);
        if ($rating) {
            return $rating->valor;
        }
        return false;
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
}
