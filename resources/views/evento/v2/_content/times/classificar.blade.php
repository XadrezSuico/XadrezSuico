@if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="alert alert-success" id="processo_sucesso" style="display:none">
        <h4><strong>Tudo certo!</strong></h4>
        As premiações de times foram classificadas com sucesso!
    </div>
    <div class="alert alert-warning" id="processo_aguarde">
        <h4><strong>Aguarde... É um processo que pode demorar...</strong></h4>
        O processo de cálculo é demorado e pode demorar alguns minutos. Que tal tomar uma xícara de café enquanto isso? <i class="fa fa-coffee"></i>
    </div>
    <div class="alert alert-danger" id="processo_erro" style="display:none">
        <h4><strong>Ocorreu algo de errado...</strong></h4>
        Algum dos processos não deu certo... Verifique qual deles abaixo, ele estará com o seguinte ícone: <i class="fa fa-times"></i>
    </div>
    <div class="box">
        <div class="box-body">
            <ul>
                @foreach($evento->event_team_awards()->where([["is_can_calculate","=",true]])->get() as $premiacao_time)
                    @if(!$premiacao_time->hasConfig("no_classificate"))
                        <li>
                            <h3>Premiação: {{$premiacao_time->name}} <i id="time_award_{{$premiacao_time->id}}_icon" style="display:none;" class="fa fa-spinner"></i></h3>
                            <ul>
                                <li>
                                    <h5>Somar Pontuação dos Enxadristas Representantes <i id="time_award_{{$premiacao_time->id}}_1_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                </li>
                                <li>
                                    <h5>Geração de Critérios de Desempate <i id="time_award_{{$premiacao_time->id}}_2_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                </li>
                                <li>
                                    <h5>Classificação dos Times <i id="time_award_{{$premiacao_time->id}}_3_icon" style="display:none;" class="fa fa-spinner"></i></h5>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
