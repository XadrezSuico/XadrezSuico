<!-- Main row -->
	<ul class="nav nav-pills">
		 @if($element->isEvent())
            <li role="presentation"><a href="{{url("/classificator/event_group/".$element->id."/".$event_classificates->id."/rule/new")}}">Nova Regra</a></li>
        @endif
	</ul>
	<div class="row">
  <section class="col-lg-12 connectedSortable">


		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Editar Regra</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="type">Tipo de Regra</label>
                        <select name="type" id="type" class="form-control width-100">
                            <option value="" selected>-- Selecione --</option>
                            @foreach(\App\Enum\ClassificationTypeRule::list() as $key => $type)
                                <option value="{{$key}}">{{$type["name"]}}</option>
                            @endforeach
                        </select>
                    </div>
					<div class="form-group" id="value_block">
						<label for="value">Valor</label>
						<input name="value" id="value" class="form-control" type="text" value="{{$event_classificate_rule->value}}"/>
                    </div>
					<div class="form-group" id="event_block">
						<label for="event_id">Evento</label>
                        <select name="event_id" id="event_id" class="form-control width-100">
                            <option value="">-- Selecione --</option>
                            @foreach(\App\Evento::all() as $event_item)
                                <option value="{{$event_item->id}}">#{{$event_item->id}} - {{$event_item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr/>
                    <label>Regras para Funcionamento da Regra:</label>
                    <div class="row">
                        @foreach(ClassificationTypeRuleConfig::list() as $key => $type_config)
					        <div class="col-md-6">
                                <div class="form-group">
                                    @switch($type_config["type"])
                                        @case("text")
                                        @case("integer")
                                            <label for="config_{{$key}}">{{$type_config["name"]}}</label>
                                        @break
                                    @endswitch
                                    @switch($type_config["type"])
                                        @case("text")
                                        @case("integer")
                                            <input type="text" id="config_{{$key}}" name="config_{{$key}}" class="form-control" value="{{$event_classificate_rule->getConfig($key,true)}}"/>
                                        @break
                                        @case("boolean")
                                            <label><input type="checkbox" id="config_{{$key}}" name="config_{{$key}}" autocomplete="off" @if($event_classificate_rule->getConfig($key,true)) checked @endif/> {{$type_config["name"]}}</label><br/>
                                        @break
                                    @endswitch
                                    <small>{{$type_config["description"]}}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
					</form>
		</div>
  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->
