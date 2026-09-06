@extends('adminlte::page')

@section('title', 'Grupo de Evento > Classificar Evento')

@section('content_header')
    <h1>Grupo de Evento > Classificar Evento</h1>
@stop

@section("css")
<style>
    .fa-spinner{
        color: orange;
    }
    .fa-times{
        color: red;
    }
    .alert-danger .fa-times{
        color: white;
    }
    .fa-check{
        color: green;
    }
</style>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="alert alert-success" id="processo_sucesso" style="display:none">
        <h4><strong>Tudo certo!</strong></h4>
        O grupo de evento foi classificado com sucesso!
    </div>
    <div class="alert alert-warning" id="processo_aguarde">
        <h4><strong>Aguarde... É um processo que pode demorar...</strong></h4>
        O processo de cálculo é demorado e pode demorar alguns minutos. Que tal tomar uma xícara de café enquanto isso? <i class="fa fa-coffee"></i>
    </div>
    <div class="alert alert-danger" id="processo_erro" style="display:none">
        <h4><strong>Ocorreu algo de errado...</strong></h4>
        Algum dos processos não deu certo... Verifique qual deles abaixo, ele estará com o seguinte ícone: <i class="fa fa-times"></i>
    </div>
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/grupoevento/dashboard/".$grupo_evento->id)}}">Voltar ao Grupo de Evento</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <ul>
                @if($grupo_evento->classificaIndividualGeral())
                    <li>
                        <h3>Classificação Geral (cross-categoria) <i id="geral_icon" style="display:none;" class="fa fa-spinner"></i></h3>
                        <ul>
                            <li>
                                <h5>Recalcular pontos das etapas <i id="geral_1_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                            </li>
                            <li>
                                <h5>Somar Pontuações das Etapas <i id="geral_2_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                            </li>
                            <li>
                                <h5>Geração de Critérios de Desempate <i id="geral_3_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                            </li>
                            <li>
                                <h5>Classificação Geral <i id="geral_4_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                            </li>
                        </ul>
                    </li>
                @endif
                @if($grupo_evento->classificaIndividualPorCategoria())
                    @foreach($grupo_evento->categorias->all() as $categoria)
                        @if(!$categoria->nao_classificar)
                            <li>
                                <h3>Categoria: {{$categoria->name}} <i id="categoria_{{$categoria->id}}_icon" style="display:none;" class="fa fa-spinner"></i></h3>
                                <ul>
                                    <li>
                                        <h5>Somar Pontuações das Etapas <i id="categoria_{{$categoria->id}}_1_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                    </li>
                                    <li>
                                        <h5>Geração de Critérios de Desempate <i id="categoria_{{$categoria->id}}_2_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                    </li>
                                    <li>
                                        <h5>Classificação da Categoria <i id="categoria_{{$categoria->id}}_3_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    categorias = [];
    erro = false;
    classificaPorCategoria = {{ $grupo_evento->classificaIndividualPorCategoria() ? 'true' : 'false' }};
    classificaGeral = {{ $grupo_evento->classificaIndividualGeral() ? 'true' : 'false' }};
    geralAbortado = false;

    @php($j = 0)
    @if($grupo_evento->classificaIndividualPorCategoria())
        @foreach($grupo_evento->categorias->all() as $categoria)
            @if(!$categoria->nao_classificar)
                categorias[{{$j++}}] = {{$categoria->id}};
            @endif
        @endforeach
    @endif

    $(document).ready(function(){
        setTimeout(function(){
            start();
        },1000);
    });

    function marcarIcone(id, sucesso){
        var $icone = $("#".concat(id));
        $icone.show(200);
        $icone.removeClass('fa-spinner fa-check fa-times');
        $icone.addClass(sucesso ? 'fa-check' : 'fa-times');
        if(!sucesso){
            erro = true;
        }
    }

    function start(){
        if (classificaPorCategoria && categorias.length > 0) {
            proxima_categoria(0);
        } else if (classificaGeral) {
            execute_geral(1);
        } else {
            muda_alerta();
        }
    }

    function proxima_categoria(i){
        $("#categoria_".concat(categorias[i]).concat("_icon")).show(200);
        execute(i,1);
    }

    function execute(i,action){
        switch(action){
            case 1:
            case 2:
                $("#categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon")).show(200);
                $.getJSON('{{url("/grupoevento/classificar/".$grupo_evento->id."/call")}}'.concat('/').concat(categorias[i]).concat('/').concat(action))
                    .done(function(data){
                        marcarIcone("categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon"), data.ok == 1);
                        execute(i,action + 1);
                    })
                    .fail(function(){
                        marcarIcone("categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon"), false);
                        execute(i,action + 1);
                    });
                break;
            case 3:
                $("#categoria_".concat(categorias[i]).concat("_3_icon")).show(200);
                $.getJSON('{{url("/grupoevento/classificar/".$grupo_evento->id."/call")}}'.concat('/').concat(categorias[i]).concat('/').concat(action))
                    .done(function(data){
                        marcarIcone("categoria_".concat(categorias[i]).concat("_3_icon"), data.ok == 1);

                        setTimeout(function(){
                            if(
                                $("#categoria_".concat(categorias[i]).concat("_1_icon")).hasClass('fa-check') &&
                                $("#categoria_".concat(categorias[i]).concat("_2_icon")).hasClass('fa-check') &&
                                $("#categoria_".concat(categorias[i]).concat("_3_icon")).hasClass('fa-check')
                            ){
                                marcarIcone("categoria_".concat(categorias[i]).concat("_icon"), true);
                            }else{
                                marcarIcone("categoria_".concat(categorias[i]).concat("_icon"), false);
                            }

                            if((i+1) < categorias.length){
                                proxima_categoria(i+1);
                            }else if(classificaGeral){
                                execute_geral(1);
                            }else{
                                muda_alerta();
                            }
                        },700);
                    })
                    .fail(function(){
                        marcarIcone("categoria_".concat(categorias[i]).concat("_3_icon"), false);
                        marcarIcone("categoria_".concat(categorias[i]).concat("_icon"), false);
                        setTimeout(function(){
                            if((i+1) < categorias.length){
                                proxima_categoria(i+1);
                            }else if(classificaGeral){
                                execute_geral(1);
                            }else{
                                muda_alerta();
                            }
                        },700);
                    });
        }
    }

    function execute_geral(action){
        if(geralAbortado){
            return;
        }

        if(action === 1){
            $("#geral_icon").show(200);
        }

        $("#geral_".concat(action).concat("_icon")).show(200);
        $("#geral_".concat(action).concat("_icon")).removeClass('fa-check fa-times').addClass('fa-spinner');

        $.getJSON('{{url("/grupoevento/classificar/".$grupo_evento->id."/call/geral")}}'.concat('/').concat(action))
            .done(function(data){
                marcarIcone("geral_".concat(action).concat("_icon"), data.ok == 1);

                if(data.ok != 1){
                    abortarGeral(action);
                    return;
                }

                if(action < 4){
                    execute_geral(action + 1);
                }else{
                    finalizarGeral();
                }
            })
            .fail(function(){
                marcarIcone("geral_".concat(action).concat("_icon"), false);
                abortarGeral(action);
            });
    }

    function abortarGeral(ultimoAction){
        geralAbortado = true;
        for(var a = ultimoAction + 1; a <= 4; a++){
            $("#geral_".concat(a).concat("_icon")).hide();
        }
        marcarIcone("geral_icon", false);
        muda_alerta();
    }

    function finalizarGeral(){
        setTimeout(function(){
            if(
                $("#geral_1_icon").hasClass('fa-check') &&
                $("#geral_2_icon").hasClass('fa-check') &&
                $("#geral_3_icon").hasClass('fa-check') &&
                $("#geral_4_icon").hasClass('fa-check')
            ){
                marcarIcone("geral_icon", true);
            }else{
                marcarIcone("geral_icon", false);
            }
            muda_alerta();
        },700);
    }

    function muda_alerta(){
        $("#processo_aguarde").hide(400);
        if(!erro){
            $("#processo_sucesso").show(400);
        }else{
            $("#processo_erro").show(400);
        }
    }
</script>
@endsection
