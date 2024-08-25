@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Resultados')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados</h1>
@stop

@section('content')
	@if (session('status'))
		<div class="alert alert-success">
				{{ session('status') }}
		</div>
	@endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/dashboard/".$evento->id)}}">Voltar à Dashboard do Evento</a></li>
        </ul>
	@endif

    @if($evento->classifica)
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Os nomes em <strong><span style="color: green"><u>verde e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->name}}</strong>.</p>
            <p>Já os nomes em <strong><span style="color: orange"><u>laranja e sublinhados</u></span></strong> estão classificados para uma etapa do <strong>{{$evento->classifica->grupo_evento->name}}</strong> do mesmo dia por <strong><u>outro evento classificatório</u></strong>.</p>
            @if($evento->classifica->grupo_evento->evento_classifica) <p>E os nomes em <strong><span style="color: red"><u>vermelho e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->grupo_evento->evento_classifica->name}}</strong> e por isso <strong><u>não podem mais se classificar</u></strong> para algum evento do <strong>{{$evento->classifica->grupo_evento->name}}</strong>.</p> @endif
            <p>A <strong>lista de classificados pode sofrer alterações</strong> devido caso ocorra declínio por parte de algum(a) enxadrista, caso permitido assim pela organização ou pelo regulamento.</p>
        </div>
    @endif
    @if($evento->grupo_evento->evento_classifica)
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Os nomes em <strong><span style="color: green"><u>verde e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->grupo_evento->evento_classifica->name}}</strong>.</p>
            <p>A <strong>lista de classificados pode sofrer alterações</strong> devido caso ocorra declínio por parte de algum(a) enxadrista, caso permitido assim pela organização ou pelo regulamento.</p>
        </div>
    @endif
    @if($evento->event_classificates()->count())
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Este evento classifica para os seguintes eventos:</p>
            <ul>
                @foreach($evento->event_classificates->all() as $event_classificates)
                    <li>{{$event_classificates->event->name}}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="box">
        <div class="box-body">

            <ul class="nav nav-tabs" role="tablist">
                @php($i = 0)
                @foreach($evento->categorias->all() as $categoria)
                    <li role="presentation" class=" @if($i++ == 0) active @endif"><a id="tab_categoria_{{$categoria->categoria->id}}" href="#categoria_{{$categoria->categoria->id}}" aria-controls="categoria_{{$categoria->categoria->id}}" role="tab" data-toggle="tab" data-id="{{$categoria->categoria->id}}" class="tab-category-item">{{$categoria->categoria->name}}</a></li>
                @endforeach
            </ul>


            <div class="tab-content">
                @php($i = 0)
                @foreach($evento->categorias->all() as $categoria)
                    <div role="tabpanel" class="tab-pane @if($i++ == 0) active @endif" id="categoria_{{$categoria->categoria->id}}">
                        <div class="icon-loading">
                            <i class="fa fa-refresh fa-spin fa-5x" aria-hidden="true"></i>
                        </div>
                        <div class="data" style="display:none">
                        </div>
                    </div>
                @endforeach
            </div>
		</div>
	</div>
@endsection
@section("css")
<style>
    .icon-loading{
        text-align: center;
        padding: 2rem;
    }

    .tab-content{
        padding: 1rem;
    }
</style>
@endsection
@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(".tab-category-item").on("click",function(){
        selectCategory($(this).data("id"));
    });

    category_selected_id = null;
    category_timeout = null;
    category_timeout_id = 1;
    function selectCategory(id){
        if(category_selected_id == id) return;

        if(category_timeout > 0){
            clearTimeout(category_timeout);
            console.log("Cancelando TimeOut: ".concat(category_timeout));
            category_timeout = null;
        }

        $("#categoria_".concat(id).concat(" .icon-loading")).css("display","block");
        $("#categoria_".concat(id).concat(" .data")).css("display","none");

        if($("#categoria_".concat(id).concat(" .data")).html().trim().length > 0){
            $("#categoria_".concat(id).concat(" .icon-loading")).css("display","none");
            $("#categoria_".concat(id).concat(" .data")).css("display","block");

            return;
        }

        category_timeout = setTimeout(() => {
            clearTimeout(category_timeout);
            category_timeout = null;

            $.ajax({
                type: "get",
                url: "{{url("/evento/{$evento->id}/api/resultados")}}/".concat(id),
                dataType: "html",
                success: function(html){
                    $("#categoria_".concat(id).concat(" .data")).html(html);

                    $("#categoria_".concat(id).concat(" .icon-loading")).css("display","none");
                    $("#categoria_".concat(id).concat(" .data")).css("display","block");
                },
                error: () => {

                    $("#categoria_".concat(id).concat(" .icon-loading")).css("display","none");
                    $("#categoria_".concat(id).concat(" .data")).css("display","block");
                }
            });


        }, 700);

        category_selected_id = id;
    }

    @if($evento->categorias()->count() > 0)
        selectCategory({{$evento->categorias->first()->categoria->id}})
    @endif
</script>
@endsection
