<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Estado;

class SeedEstadosBR extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $estado_12 = new Estado; $estado_12->id = '1'; $estado_12->nome = 'Acre'; $estado_12->abbr = 'AC'; $estado_12->ibge_id = '12'; $estado_12->pais_id = 33; $estado_12->save();
        $estado_27 = new Estado; $estado_27->id = '2'; $estado_27->nome = 'Alagoas'; $estado_27->abbr = 'AL'; $estado_27->ibge_id = '27'; $estado_27->pais_id = 33; $estado_27->save();
        $estado_16 = new Estado; $estado_16->id = '3'; $estado_16->nome = 'Amapá'; $estado_16->abbr = 'AP'; $estado_16->ibge_id = '16'; $estado_16->pais_id = 33; $estado_16->save();
        $estado_13 = new Estado; $estado_13->id = '4'; $estado_13->nome = 'Amazonas'; $estado_13->abbr = 'AM'; $estado_13->ibge_id = '13'; $estado_13->pais_id = 33; $estado_13->save();
        $estado_29 = new Estado; $estado_29->id = '5'; $estado_29->nome = 'Bahia'; $estado_29->abbr = 'BA'; $estado_29->ibge_id = '29'; $estado_29->pais_id = 33; $estado_29->save();
        $estado_23 = new Estado; $estado_23->id = '6'; $estado_23->nome = 'Ceará'; $estado_23->abbr = 'CE'; $estado_23->ibge_id = '23'; $estado_23->pais_id = 33; $estado_23->save();
        $estado_53 = new Estado; $estado_53->id = '7'; $estado_53->nome = 'Distrito Federal'; $estado_53->abbr = 'DF'; $estado_53->ibge_id = '53'; $estado_53->pais_id = 33; $estado_53->save();
        $estado_32 = new Estado; $estado_32->id = '8'; $estado_32->nome = 'Espírito Santo'; $estado_32->abbr = 'ES'; $estado_32->ibge_id = '32'; $estado_32->pais_id = 33; $estado_32->save();
        $estado_52 = new Estado; $estado_52->id = '9'; $estado_52->nome = 'Goiás'; $estado_52->abbr = 'GO'; $estado_52->ibge_id = '52'; $estado_52->pais_id = 33; $estado_52->save();
        $estado_21 = new Estado; $estado_21->id = '10'; $estado_21->nome = 'Maranhão'; $estado_21->abbr = 'MA'; $estado_21->ibge_id = '21'; $estado_21->pais_id = 33; $estado_21->save();
        $estado_51 = new Estado; $estado_51->id = '11'; $estado_51->nome = 'Mato Grosso'; $estado_51->abbr = 'MT'; $estado_51->ibge_id = '51'; $estado_51->pais_id = 33; $estado_51->save();
        $estado_50 = new Estado; $estado_50->id = '12'; $estado_50->nome = 'Mato Grosso do Sul'; $estado_50->abbr = 'MS'; $estado_50->ibge_id = '50'; $estado_50->pais_id = 33; $estado_50->save();
        $estado_31 = new Estado; $estado_31->id = '13'; $estado_31->nome = 'Minas Gerais'; $estado_31->abbr = 'MG'; $estado_31->ibge_id = '31'; $estado_31->pais_id = 33; $estado_31->save();
        $estado_15 = new Estado; $estado_15->id = '14'; $estado_15->nome = 'Pará'; $estado_15->abbr = 'PA'; $estado_15->ibge_id = '15'; $estado_15->pais_id = 33; $estado_15->save();
        $estado_25 = new Estado; $estado_25->id = '15'; $estado_25->nome = 'Paraíba'; $estado_25->abbr = 'PB'; $estado_25->ibge_id = '25'; $estado_25->pais_id = 33; $estado_25->save();
        $estado_41 = new Estado; $estado_41->id = '16'; $estado_41->nome = 'Paraná'; $estado_41->abbr = 'PR'; $estado_41->ibge_id = '41'; $estado_41->pais_id = 33; $estado_41->save();
        $estado_26 = new Estado; $estado_26->id = '17'; $estado_26->nome = 'Pernambuco'; $estado_26->abbr = 'PE'; $estado_26->ibge_id = '26'; $estado_26->pais_id = 33; $estado_26->save();
        $estado_22 = new Estado; $estado_22->id = '18'; $estado_22->nome = 'Piauí'; $estado_22->abbr = 'PI'; $estado_22->ibge_id = '22'; $estado_22->pais_id = 33; $estado_22->save();
        $estado_33 = new Estado; $estado_33->id = '19'; $estado_33->nome = 'Rio de Janeiro'; $estado_33->abbr = 'RJ'; $estado_33->ibge_id = '33'; $estado_33->pais_id = 33; $estado_33->save();
        $estado_24 = new Estado; $estado_24->id = '20'; $estado_24->nome = 'Rio Grande do Norte'; $estado_24->abbr = 'RN'; $estado_24->ibge_id = '24'; $estado_24->pais_id = 33; $estado_24->save();
        $estado_43 = new Estado; $estado_43->id = '21'; $estado_43->nome = 'Rio Grande do Sul'; $estado_43->abbr = 'RS'; $estado_43->ibge_id = '43'; $estado_43->pais_id = 33; $estado_43->save();
        $estado_11 = new Estado; $estado_11->id = '22'; $estado_11->nome = 'Rondônia'; $estado_11->abbr = 'RO'; $estado_11->ibge_id = '11'; $estado_11->pais_id = 33; $estado_11->save();
        $estado_14 = new Estado; $estado_14->id = '23'; $estado_14->nome = 'Roraima'; $estado_14->abbr = 'RR'; $estado_14->ibge_id = '14'; $estado_14->pais_id = 33; $estado_14->save();
        $estado_42 = new Estado; $estado_42->id = '24'; $estado_42->nome = 'Santa Catarina'; $estado_42->abbr = 'SC'; $estado_42->ibge_id = '42'; $estado_42->pais_id = 33; $estado_42->save();
        $estado_35 = new Estado; $estado_35->id = '25'; $estado_35->nome = 'São Paulo'; $estado_35->abbr = 'SP'; $estado_35->ibge_id = '35'; $estado_35->pais_id = 33; $estado_35->save();
        $estado_28 = new Estado; $estado_28->id = '26'; $estado_28->nome = 'Sergipe'; $estado_28->abbr = 'SE'; $estado_28->ibge_id = '28'; $estado_28->pais_id = 33; $estado_28->save();
        $estado_17 = new Estado; $estado_17->id = '27'; $estado_17->nome = 'Tocantins'; $estado_17->abbr = 'TO'; $estado_17->ibge_id = '17'; $estado_17->pais_id = 33; $estado_17->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
