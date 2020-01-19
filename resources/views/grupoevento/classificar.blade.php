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
            </ul>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    categorias = [];
    erro = false;
    @php($j = 0)
    @foreach($grupo_evento->categorias->all() as $categoria)
        @if(!$categoria->nao_classificar)
            categorias[{{$j++}}] = {{$categoria->id}};
        @endif
    @endforeach

    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });
        setTimeout(function(){
            start();
        },1000);
    });

    function start(){
        proxima_categoria(0);
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
                $.getJSON('{{url("/grupoevento/classificar/".$grupo_evento->id."/call")}}'.concat('/').concat(categorias[i]).concat('/').concat(action),function(data){
                    if(data.ok == 1){
                        $("#categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon")).removeClass('fa-spinner');
                        $("#categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon")).addClass('fa-check');
                    }else{
                        $("#categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon")).removeClass('fa-spinner');
                        $("#categoria_".concat(categorias[i]).concat("_").concat(action).concat("_icon")).addClass('fa-times');
                    }
                    execute(i,action + 1);
                });
                break;
            case 3:
                $("#categoria_".concat(categorias[i]).concat("_").concat(3).concat("_icon")).show(200);
                $.getJSON('{{url("/grupoevento/classificar/".$grupo_evento->id."/call")}}'.concat('/').concat(categorias[i]).concat('/').concat(action),function(data){
                    if(data.ok == 1){
                        $("#categoria_".concat(categorias[i]).concat("_").concat(3).concat("_icon")).removeClass('fa-spinner');
                        $("#categoria_".concat(categorias[i]).concat("_").concat(3).concat("_icon")).addClass('fa-check');
                    }else{
                        $("#categoria_".concat(categorias[i]).concat("_").concat(3).concat("_icon")).removeClass('fa-spinner');
                        $("#categoria_".concat(categorias[i]).concat("_").concat(3).concat("_icon")).addClass('fa-times');
                    }

                    setTimeout(function(){
                        if(
                            $("#categoria_".concat(categorias[i]).concat("_1_icon")).hasClass('fa-check') &&
                            $("#categoria_".concat(categorias[i]).concat("_2_icon")).hasClass('fa-check') &&
                            $("#categoria_".concat(categorias[i]).concat("_3_icon")).hasClass('fa-check')
                        ){
                            $("#categoria_".concat(categorias[i]).concat("_icon")).removeClass('fa-spinner');
                            $("#categoria_".concat(categorias[i]).concat("_icon")).addClass('fa-check');
                        }else{
                            $("#categoria_".concat(categorias[i]).concat("_icon")).removeClass('fa-spinner');
                            $("#categoria_".concat(categorias[i]).concat("_icon")).addClass('fa-times');
                            erro = true;
                        }


                        if((i+1) < categorias.length){
                            proxima_categoria(i+1);
                        }else{
                            muda_alerta();
                        }
                    },700);
                });
        }
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
