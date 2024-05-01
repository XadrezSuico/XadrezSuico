<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\MessageBag;

use App\Enxadrista;
use App\TipoDocumento;
use App\Documento;
use App\Cidade;
use App\Clube;
use App\Categoria;
use App\Enum\ConfigType;
use App\Inscricao;
use App\Evento;
use App\Sexo;

use App\Http\Util\Util;

use DateTime;
use Illuminate\Support\Facades\Log;

class SportAppIngaDigitalImport implements OnEachRow, WithHeadingRow
{
    private $event_id;
    private $event;
    private $datetime;

    private $modalidades = [];
    public function __construct($event_id){
        $this->event_id = $event_id;
        $this->event = Evento::find($event_id);

        if($this->event->tipo_modalidade == 0){
            $this->modalidades[] = mb_strtoupper("Convencional");
        }else {
            $this->modalidades[] = mb_strtoupper("Relâmpago");
            $this->modalidades[] = mb_strtoupper("Rápido");
        }

        Log::debug("SportAppIngaDigitalImport - Início");
    }
    public function onRow(Row $Row)
    {
        $row = $Row->toArray();

        Log::debug("Row: " . json_encode($row));

        if (!isset($row["categoria_de_modalidade"])) {
            Log::debug("Linha desconsiderada: FALTA CATEGORIA DE MODALIDADE");
            return null;
        }
        if (!isset($row["modalidade"])) {
            Log::debug("Linha desconsiderada: FALTA MODALIDADE");
            return null;
        }

        if(!in_array(mb_strtoupper($row["modalidade"]),$this->modalidades)) {
            Log::debug("Linha desconsiderada: MODALIDADE NÃO ATENDE ESTE TORNEIO - ". mb_strtoupper($row["modalidade"])." (".implode(",",$this->modalidades).")");
            return null;
        }

        $club_name = trim($row["delegacaoequipe"]);
        $sex_name = trim($row["naipe"]);
        $age_category = trim($row["categoria"]);

        $name = mb_strtoupper(trim($row["nome"]));
        $cpf_or_ext = trim($row["cpf_doc_estr"]);
        $rg = trim($row["rg"]);
        $bornday = trim($row["data_nascimento"]);
        $email = trim($row["email"]);
        $phone = trim($row["telefone"]);

        $city_name = (isset($row["municipio_de_origem"])) ? $row["municipio_de_origem"] : null;

        $datetime = DateTime::createFromFormat('d/m/Y', $bornday);

        if ($datetime) {
            Log::debug("datetime ok - ".$datetime->format("Y-m-d"));
            $this->datetime = $datetime;
        } else {
            Log::debug("datetime error - ".$bornday);
        }

        $city = null;
        if($city_name){
            $count_city = 0;

            if(env("ESTADO_DEFAULT",false)){
                $count_city = Cidade::where([["name","=",$city_name],["estados_id","=",env("ESTADO_DEFAULT")]])->count();
            }else{
                $count_city = Cidade::where([["name","=",$city_name]])->count();
            }

            if($count_city > 0){


                if(env("ESTADO_DEFAULT",false)){
                    $city = Cidade::where([["name","=",$city_name],["estados_id","=",env("ESTADO_DEFAULT")]])->first();
                }else{
                    $city = Cidade::where([["name","=",$city_name]])->first();
                }
            }
        }

        if(!$this->hasClub($club_name, $city)){
            $this->registerClub($club_name, $city);
        }
        $club = $this->getClub($club_name, $city);

        $is_cpf = $this->isCPF($cpf_or_ext);

        if(!$this->hasEnxadrista($name,$bornday,($is_cpf) ? $cpf_or_ext : null,(!$is_cpf) ? $cpf_or_ext : null,$rg)){
            if($city){
                $this->registerEnxadrista($name,$bornday,($is_cpf) ? $cpf_or_ext : null,(!$is_cpf) ? $cpf_or_ext : null,$rg, $email, $phone, $city->id, $club->id);
            }else{
                $this->registerEnxadrista($name,$bornday,($is_cpf) ? $cpf_or_ext : null,(!$is_cpf) ? $cpf_or_ext : null,$rg, $email, $phone, null, $club->id);
            }
        }

        if($this->hasEnxadrista($name,$bornday,($is_cpf) ? $cpf_or_ext : null,(!$is_cpf) ? $cpf_or_ext : null,$rg)){
            $enxadrista = $this->getEnxadrista($name,$bornday,($is_cpf) ? $cpf_or_ext : null,(!$is_cpf) ? $cpf_or_ext : null,$rg);
            if($enxadrista){
                if(!$enxadrista->sexo){
                    if(Sexo::where([["sex_from_import","=",$sex_name]])->count() > 0){
                        $enxadrista->sexos_id = Sexo::where([["sex_from_import","=",$sex_name]])->first()->id;
                        $enxadrista->save();
                    }
                }
                if($this->hasCategory($sex_name,$age_category)){
                    $category = $this->getCategory($sex_name,$age_category);

                    if($this->hasTournament($category->id)){
                        $tournament = $this->getTournament($category->id);

                        if(!$this->event->enxadristaInscrito($enxadrista->id)){
                            $inscricao = new Inscricao;
                            $inscricao->enxadrista_id = $enxadrista->id;
                            $inscricao->categoria_id = $category->id;
                            $inscricao->cidade_id = $this->event->cidade->id;
                            $inscricao->clube_id = $club->id;
                            $inscricao->torneio_id = $tournament->id;

                            if($city){
                                $inscricao->cidade_id = $city->id;
                            }

                            $inscricao->regulamento_aceito = true;
                            $inscricao->xadrezsuico_aceito = false;
                            $inscricao->is_aceito_imagem = 0;

                            $inscricao->confirmado = 0;
                        }else{
                            $inscricao = $this->event->enxadristaInscrito($enxadrista->id);
                        }

                        switch ($this->event->tipo_modalidade) {
                            case 0:
                                // Convencional
                                Log::debug(mb_strtoupper($row["modalidade"]) . " == CONVENCIONAL");

                                if (mb_strtoupper($row["modalidade"]) == "CONVENCIONAL") {
                                    $inscricao->confirmado = 1;
                                }

                                break;

                            case 1:
                                // Rápido
                                Log::debug(mb_strtoupper($row["modalidade"]) . " == RÁPIDO");
                                if (mb_strtoupper($row["modalidade"]) == "RÁPIDO") {
                                    $inscricao->confirmado = 1;
                                }
                                break;

                            case 2:
                                // Relâmpago
                                Log::debug(mb_strtoupper($row["modalidade"]) . " == RELÂMPAGO");
                                if (mb_strtoupper($row["modalidade"]) == "RELÂMPAGO") {
                                    $inscricao->confirmado = 1;
                                }
                        }

                        $inscricao->save();

                        if(mb_strtoupper($row["modalidade"]) == "CONVENCIONAL" && $this->event->tipo_modalidade == 0){
                            switch(mb_strtoupper($row["escalacao"])) {
                                case mb_strtoupper("1º Tabuleiro"):
                                    $inscricao->setConfig("team_order", ConfigType::Integer, 1);
                                    break;
                                case mb_strtoupper("2º Tabuleiro"):
                                    $inscricao->setConfig("team_order", ConfigType::Integer, 2);
                                    break;
                                case mb_strtoupper("3º Tabuleiro"):
                                    $inscricao->setConfig("team_order", ConfigType::Integer, 3);
                                    break;
                                default:
                                    if(strlen(mb_strtoupper($row["escalacao"])) > 0) {
                                        $inscricao->setConfig("team_order", ConfigType::Integer, 4);
                                    }else{
                                        $inscricao->setConfig("team_order", ConfigType::Integer, 0);
                                    }
                            }
                        }
                    }else{
                        $data = "event_id=".$this->event_id."&enxadrista_id=".$enxadrista->id."&sex_name=".$sex_name."&age_category=".$age_category."&category_id=".$category->id;
                        Log::debug("SportAppIngaDigitalImport - Erro de Importação de Registro: Torneio não encontrado com a categoria selecionada para o evento. Data: ".$data);
                    }



                }else{
                    $data = "event_id=".$this->event_id."&enxadrista_id=".$enxadrista->id."&sex_name=".$sex_name."&age_category=".$age_category;
                    Log::debug("SportAppIngaDigitalImport - Erro de Importação de Registro: Categoria não encontrada para o evento. Data: ".$data);
                }
            }else{
                $data = "event_id=".$this->event_id."&sex_name=".$sex_name."&age_category=".$age_category;
                Log::debug("SportAppIngaDigitalImport - Erro de Importação de Registro: Enxadrista nulo. Data: ".$data);
            }
        }else{
            $data = "event_id=".$this->event_id."&sex_name=".$sex_name."&age_category=".$age_category;
            Log::debug("SportAppIngaDigitalImport - Erro de Importação de Registro: Enxadrista não cadastrado/encontrado. Data: ".$data);
        }
    }

    public function isCPF($cpf){

        $documento = Util::numeros($cpf);

        $documento_len = strlen($documento);

        // tamanho
        if($documento_len < 11){
            return ["ok"=>0,"error"=>1,"message"=>"O documento informado é muito curto."];
        }

        // caracteres
        $crc1 = substr($documento,0,1);
        $all_caracts_is_same = true;
        for($i = 1; $i < $documento_len; $i++){
            if($crc1 != substr($documento,$i,1)){
                $all_caracts_is_same = false;
            }
        }

        if($all_caracts_is_same){
            return false;
        }

        if(
            count(explode("NAO",strtoupper($documento))) > 1 ||
            count(explode("NÃO",strtoupper($documento))) > 1 ||
            count(explode("TENHO",strtoupper($documento))) > 1 ||
            count(explode("TEM",strtoupper($documento))) > 1 ||
            count(explode("TEM",strtoupper($documento))) > 1
        ){
            return false;
        }


        $validator = \Validator::make(["cpf"=>$cpf], [
            'cpf' => 'required|cpf',
        ]);
        if ($validator->fails()) {
            return false;
        }

        return true;
    }

    public function parseName($name){
        // Algoritmo para eliminar os problemas com espaçamentos duplos ou até triplos.
        $nome_corrigido = "";

        $part_names = explode(" ", mb_strtoupper($name));
        foreach ($part_names as $part_name) {
            if ($part_name != ' ') {
                $trim = trim($part_name);
                if (strlen($trim) > 0) {
                    $nome_corrigido .= $trim;
                    $nome_corrigido .= " ";
                }
            }
        }

        return $nome_corrigido;
    }

    public function hasEnxadrista($name,$bornday,$cpf=null,$ext_document = null, $rg=null){
        Log::debug("hasEnxadrista");
        if($cpf){
            Log::debug("hasEnxadrista-cpf");
            if(Documento::where([
                ["tipo_documentos_id","=",1],
                ["numero","=",Util::numeros($cpf)]
            ])
            ->whereHas("enxadrista",function($q1){
                $q1->whereDoesntHave("configs",function($q2){
                    $q2->where([["key","=","united_to"]]);
                });
            })
            ->count() > 0){
                Log::debug("hasEnxadrista-cpf-found");
                $documento = Documento::where([
                    ["tipo_documentos_id","=",1],["numero","=",Util::numeros($cpf)]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })->first();

                $enxadrista = Enxadrista::find($documento->enxadrista_id);
                if($rg){
                    if(!$enxadrista->hasDocument(2,$rg)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 2;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = $rg;
                        $document->save();
                    }
                }
                return true;
            }
        }
        if($ext_document){
            Log::debug("hasEnxadrista-ext_document");
            if(Documento::where([
                ["tipo_documentos_id","=",4],["numero","=",$ext_document]
            ])
            ->whereHas("enxadrista",function($q1){
                $q1->whereDoesntHave("configs",function($q2){
                    $q2->where([["key","=","united_to"]]);
                });
            })->count() > 0){
                Log::debug("hasEnxadrista-ext_document-found");
                $documento = Documento::where([
                    ["tipo_documentos_id","=",4],["numero","=",$ext_document]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })->first();
                $enxadrista = Enxadrista::find($documento->enxadrista_id);
                if($rg){
                    if(!$enxadrista->hasDocument(2,$rg)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 2;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = $rg;
                        $document->save();
                    }
                }
                return true;
            }
        }
        if($rg){
            Log::debug("hasEnxadrista-rg");
            if(Documento::where([
                ["tipo_documentos_id","=",2],["numero","=",$rg]
            ])
            ->whereHas("enxadrista", function ($q1) {
                $q1->whereDoesntHave("configs", function ($q2) {
                    $q2->where([["key", "=", "united_to"]]);
                });
            })->count() > 0){
                Log::debug("hasEnxadrista-rg-found");
                $documento = Documento::where([
                    ["tipo_documentos_id","=",2],["numero","=",$rg]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })->first();
                $enxadrista = Enxadrista::find($documento->enxadrista_id);
                if($cpf){
                    if(!$enxadrista->hasDocument(1,$cpf)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 1;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = Util::numeros($cpf);
                        $document->save();
                    }
                }
                if($ext_document){
                    if(!$enxadrista->hasDocument(4,$ext_document)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 4;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = $ext_document;
                        $document->save();
                    }
                }
                return true;
            }
        }
        Log::debug("hasEnxadrista-name-born");
        if(Enxadrista::where([
            ["name","=",$this->parseName($name)],["born","=",$this->datetime->format("Y-m-d")]
        ])
        ->whereDoesntHave("configs", function ($q2) {
            $q2->where([["key", "=", "united_to"]]);
        })
        ->count() > 0){
            Log::debug("hasEnxadrista-name-born-found");
            $enxadrista = Enxadrista::where([
                ["name","=",$this->parseName($name)],["born","=",$this->datetime->format("Y-m-d")]
            ])
            ->whereDoesntHave("configs", function ($q2) {
                $q2->where([["key", "=", "united_to"]]);
            })
            ->first();
            if($cpf){
                if(!$enxadrista->hasDocument(1,$cpf)){
                    $document = new Documento;
                    $document->tipo_documentos_id = 1;
                    $document->enxadrista_id = $enxadrista->id;
                    $document->numero = Util::numeros($cpf);
                    $document->save();
                }
            }
            if($rg){
                if(!$enxadrista->hasDocument(2,$rg)){
                    $document = new Documento;
                    $document->tipo_documentos_id = 2;
                    $document->enxadrista_id = $enxadrista->id;
                    $document->numero = $rg;
                    $document->save();
                }
            }
            if($ext_document){
                if(!$enxadrista->hasDocument(4,$ext_document)){
                    $document = new Documento;
                    $document->tipo_documentos_id = 4;
                    $document->enxadrista_id = $enxadrista->id;
                    $document->numero = $ext_document;
                    $document->save();
                }
            }
            return true;
        }

        return false;
    }

    public function getEnxadrista($name,$bornday,$cpf=null,$ext_document = null, $rg=null){
        Log::debug("getEnxadrista");
        if($this->hasEnxadrista($name,$bornday,$cpf,$ext_document,$rg)){
            Log::debug("getEnxadrista-found");
            if($cpf){
                Log::debug("getEnxadrista-cpf");
                if(Documento::where([
                    ["tipo_documentos_id","=",1],["numero","=",Util::numeros($cpf)]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })
                ->count() > 0){
                    $documento = Documento::where([
                        ["tipo_documentos_id","=",1],["numero","=",Util::numeros($cpf)]
                    ])
                    ->whereHas("enxadrista", function ($q1) {
                        $q1->whereDoesntHave("configs", function ($q2) {
                            $q2->where([["key", "=", "united_to"]]);
                        });
                    })->first();
                    $enxadrista = Enxadrista::find($documento->enxadrista_id);
                    if($rg){
                        if(!$enxadrista->hasDocument(2,$rg)){
                            $document = new Documento;
                            $document->tipo_documentos_id = 2;
                            $document->enxadrista_id = $enxadrista->id;
                            $document->numero = $rg;
                            $document->save();
                        }
                    }
                    return $enxadrista;
                }
            }
            if($ext_document){
                Log::debug("getEnxadrista-ext_document");
                if(Documento::where([
                    ["tipo_documentos_id","=",4],["numero","=",$ext_document]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })
                ->count() > 0){
                    $documento = Documento::where([
                        ["tipo_documentos_id","=",4],["numero","=",$ext_document]
                    ])
                    ->whereHas("enxadrista", function ($q1) {
                        $q1->whereDoesntHave("configs", function ($q2) {
                            $q2->where([["key", "=", "united_to"]]);
                        });
                    })->first();
                    $enxadrista = Enxadrista::find($documento->enxadrista_id);
                    if($rg){
                        if(!$enxadrista->hasDocument(2,$rg)){
                            $document = new Documento;
                            $document->tipo_documentos_id = 2;
                            $document->enxadrista_id = $enxadrista->id;
                            $document->numero = $rg;
                            $document->save();
                        }
                    }
                    return $enxadrista;
                }
            }
            if($rg){
                Log::debug("getEnxadrista-rg");
                if(Documento::where([
                    ["tipo_documentos_id","=",2],["numero","=",$rg]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })
                ->count() > 0){
                    $documento = Documento::where([
                        ["tipo_documentos_id","=",2],["numero","=",$rg]
                    ])
                    ->whereHas("enxadrista", function ($q1) {
                        $q1->whereDoesntHave("configs", function ($q2) {
                            $q2->where([["key", "=", "united_to"]]);
                        });
                    })->first();
                    $enxadrista = Enxadrista::find($documento->enxadrista_id);
                    if($cpf){
                        if(!$enxadrista->hasDocument(1,$cpf)){
                            $document = new Documento;
                            $document->tipo_documentos_id = 1;
                            $document->enxadrista_id = $enxadrista->id;
                            $document->numero = Util::numeros($cpf);
                            $document->save();
                        }
                    }
                    if($ext_document){
                        if(!$enxadrista->hasDocument(4,$ext_document)){
                            $document = new Documento;
                            $document->tipo_documentos_id = 4;
                            $document->enxadrista_id = $enxadrista->id;
                            $document->numero = $ext_document;
                            $document->save();
                        }
                    }
                    return $enxadrista;
                }
            }
            if(Enxadrista::where([
                ["name","=",$this->parseName($name)],["born","=",$this->datetime->format("Y-m-d")]
            ])
            ->whereHas("enxadrista", function ($q1) {
                $q1->whereDoesntHave("configs", function ($q2) {
                    $q2->where([["key", "=", "united_to"]]);
                });
            })->count() > 0){
                Log::debug("getEnxadrista-name-born");
                $enxadrista = Enxadrista::where([
                    ["name","=",$this->parseName($name)],["born","=",$this->datetime->format("Y-m-d")]
                ])
                ->whereHas("enxadrista", function ($q1) {
                    $q1->whereDoesntHave("configs", function ($q2) {
                        $q2->where([["key", "=", "united_to"]]);
                    });
                })->first();

                if($cpf){
                    if(!$enxadrista->hasDocument(1,$cpf)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 1;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = Util::numeros($cpf);
                        $document->save();
                    }
                }
                if($rg){
                    if(!$enxadrista->hasDocument(2,$rg)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 2;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = $rg;
                        $document->save();
                    }
                }
                if($ext_document){
                    if(!$enxadrista->hasDocument(4,$ext_document)){
                        $document = new Documento;
                        $document->tipo_documentos_id = 4;
                        $document->enxadrista_id = $enxadrista->id;
                        $document->numero = $ext_document;
                        $document->save();
                    }
                }
                return $enxadrista;
            }
        }
        Log::debug("getEnxadrista-not-found");
        return null;
    }

    public function registerEnxadrista($name,$bornday,$cpf=null,$ext_document=null,$rg=null,$email = null,$phone = null, $city_id = null, $clubs_id){
        Log::debug("registerEnxadrista");
        if(!$this->hasEnxadrista($name,$bornday,$cpf,$ext_document,$rg)){
            $enxadrista = new Enxadrista;
            $enxadrista->pais_id = 33;
            $enxadrista->cidade_id = 4006;
            $enxadrista->clube_id = $clubs_id;
            $enxadrista->name = $this->parseName($name);
            $enxadrista->born = $this->datetime->format("Y-m-d");

            if($email){
                $enxadrista->email = $email;
            }
            if($phone){
                $enxadrista->pais_celular_id = 33;
                $enxadrista->celular = $phone;
            }
            if($city_id){
                $enxadrista->cidade_id = $city_id;
            }
            $enxadrista->save();
            if($cpf){
                $documento = new Documento;
                $documento->tipo_documentos_id = 1;
                $documento->enxadrista_id = $enxadrista->id;
                $documento->numero = Util::numeros($cpf);
                $documento->save();
            }
            if($ext_document){
                $documento = new Documento;
                $documento->tipo_documentos_id = 4;
                $documento->enxadrista_id = $enxadrista->id;
                $documento->numero = $ext_document;
                $documento->save();
            }
            if($rg){
                $documento = new Documento;
                $documento->tipo_documentos_id = 2;
                $documento->enxadrista_id = $enxadrista->id;
                $documento->numero = $rg;
                $documento->save();
            }
            return true;
        }
        return false;
    }

    public function hasClub($name, $city = null){
        if($city){
            return Clube::where([["name","=",$name],["cidade_id","=",$city->id]])->count() > 0;
        }
        return Clube::where([["name","=",$name]])->count() > 0;
    }

    public function getClub($name, $city = null){
        if($this->hasClub($name, $city)){
            if($city){
                return Clube::where([["name","=",$name],["cidade_id","=",$city->id]])->first();
            }
            return Clube::where([["name","=",$name]])->first();
        }
        return null;
    }

    public function registerClub($name, $city = null){
        if(!$this->hasClub($name)){
            $club = new Clube;
            $club->name = $name;
            $club->is_imported = true;
            $club->imported_from = "SportApp - *.xlsx";

            if($city){
                $club->cidade_id = $city->id;
            }

            $club->save();
        }
        return false;
    }

    public function hasCategory($sex_name,$age_category){
        $event_id = $this->event_id;
        return Categoria::where([["category_from_import","=",trim($age_category)]])
        ->whereHas("sexos",function($q1) use ($sex_name){
            $q1->whereHas("sexo",function($q2) use ($sex_name){
                $q2->where([["sex_from_import","=",trim($sex_name)]]);
            });
        })
        ->whereHas("eventos",function($q1) use ($event_id){
            $q1->where([["evento_id","=",$event_id]]);
        })
        ->count() > 0;
    }

    public function getCategory($sex_name,$age_category){
        if($this->hasCategory($sex_name,$age_category)){
            $event_id = $this->event_id;
            return Categoria::where([["category_from_import","=",trim($age_category)]])
            ->whereHas("sexos",function($q1) use ($sex_name){
                $q1->whereHas("sexo",function($q2) use ($sex_name){
                    $q2->where([["sex_from_import","=",trim($sex_name)]]);
                });
            })
            ->whereHas("eventos",function($q1) use ($event_id){
                $q1->where([["evento_id","=",$event_id]]);
            })
            ->first();
        }
        return null;
    }

    public function hasTournament($category_id){
        foreach ($this->event->torneios->all() as $Torneio) {
            foreach ($Torneio->categorias->all() as $categoria) {
                if ($categoria->categoria_id == $category_id) {
                    return true;
                }
            }
        }
        return false;
    }
    public function getTournament($category_id){
        if($this->hasTournament($category_id)){
            foreach ($this->event->torneios->all() as $Torneio) {
                foreach ($Torneio->categorias->all() as $categoria) {
                    if ($categoria->categoria_id == $category_id) {
                        return $Torneio;
                    }
                }
            }
        }
        return null;
    }
}
