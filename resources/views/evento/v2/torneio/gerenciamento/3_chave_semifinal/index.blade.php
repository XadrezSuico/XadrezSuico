@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.gerenciamento.3_chave_semifinal.index')
@endsection

@push('styles')
    <style>
.display-none, .displayNone{
			display: none;
		}
		.width-100{
			width: 100% !important;
		}

        .emparceiramento{
            width: 100%;
            border: 1px solid #000;
            padding: 3px;
            background: rgb(223, 223, 223);
        }

        .arrows{
            font-size: 3rem;
        }

        .enxadrista_white{
            display:inline-block;
            background: white;
            color: black;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_black{
            display:inline-block;
            background: black;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_without_color{
            display:inline-block;
            background: gray;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .btn_enxadrista_color{
            width: 100%;
            word-wrap: break-word !important;
            white-space: inherit !important;
        }
        .btn_enxadrista_color.bg-white{
            background: white;
            color: black;
        }
        .btn_enxadrista_color.bg-black{
            background: black;
            color: white;
        }

        .resultados_confrontos{
            font-size: 2rem;
        }

        .resultados_confrontos .resultado{
            display: inline-block;
            background: #d2d6de;
            border-radius: 4px;
            padding: 0.2rem 0.4rem;
            margin: 0.5rem 0;
            font-weight: bold;

        }

        .emparceiramento a{
            color: white;
            text-decoration: underline;
        }
        .enxadrista_white a{
            color: black;
        }
    </style>
@endpush

@push('event-scripts')
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		@if($tab)
			$("#tab_{{$tab}}").tab("show");
		@endif
  });

  function resultado_add(emparceiramento, enxadrista, valor){
    var resultado_atual = parseFloat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val());
    resultado_atual = resultado_atual + valor;
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val(resultado_atual);
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista).concat("_label")).html(resultado_atual);
  }
  function resultado_sub(emparceiramento, enxadrista, valor){
    var resultado_atual = parseFloat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val());
    if(resultado_atual-valor < 0){
        resultado_atual = 0;
    }else{
        resultado_atual = resultado_atual - valor;
    }
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val(resultado_atual);
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista).concat("_label")).html(resultado_atual);
  }

  function setWhite(emparceiramento, enxadrista){
      if(enxadrista == 'a'){
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).addClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_without_color");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).addClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_without_color");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).addClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).removeClass("bg-black");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).removeClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).addClass("bg-black");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val(1);
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val(2);
      }else{
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).addClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_without_color");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).addClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_without_color");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).removeClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).addClass("bg-black");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).addClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).removeClass("bg-black");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val(2);
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val(1);
      }
  }

  function enviarEmparceiramentoData(emparceiramento){
        var data = "";
        data = data.concat("emparceiramento_id=".concat(emparceiramento));
        data = data.concat("&cor_a=".concat($("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val()));
        data = data.concat("&cor_b=".concat($("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val()));
        data = data.concat("&resultado_a=".concat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val()));
        data = data.concat("&resultado_b=".concat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val()));
        data = data.concat("&_token={{ csrf_token() }}");
		$.ajax({
			type: "post",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/setEmparceiramentoData")}}",
			data: data,
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento atualizado com sucesso.',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val(data.data.resultado_b);

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a_label")).html(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b_label")).html(data.data.resultado_b);

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a_label_partida")).html(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b_label_partida")).html(data.data.resultado_b);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val()){
                    $("#homologar_emp_".concat(emparceiramento)).removeClass("display-none");
                }else{
                    $("#homologar_emp_".concat(emparceiramento)).addClass("display-none");
                }
            }
        });
  }
  function homologarEmparceiramento(emparceiramento){
        var data = "";
		$.ajax({
			type: "get",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/homologateEmparceiramento")}}/".concat(emparceiramento),
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento homologado com sucesso. Recarregando página...',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }
            }
        });
  }
  function desaprovarEmparceiramento(emparceiramento){
        var data = "";
		$.ajax({
			type: "get",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/unaproveEmparceiramento")}}/".concat(emparceiramento),
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento homologado com sucesso. Recarregando página...',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }
            }
        });
  }
</script>
@endpush
