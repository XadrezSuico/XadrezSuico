@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' ('.$evento->name.') >> Timeline >> Editar item')
@section('content_header')
    <h1>Evento #{{ $evento->id }} ({{ $evento->name }}) >> Timeline >> Editar item</h1>
@stop

@section('content')
    <ul class="nav nav-pills">
        <li role="presentation"><a href="/evento/dashboard/{{ $evento->id }}?tab=timeline">Voltar à Timeline na dashboard do evento</a></li>
    </ul>
    <br/>
    @if(session('status'))
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('status') }}
        </div>
    @endif
    <div class="row">
        <section class="col-lg-6 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Editar item #{{ $item->id }}</h3>
                </div>
                <form method="post" action="{{ url('/evento/'.$evento->id.'/timeline/edit/'.$item->id) }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="timeline_edit_title">Título</label>
                            <input name="title" id="timeline_edit_title" class="form-control" type="text" value="{{ old('title', $item->title) }}" required />
                        </div>
                        <div class="form-group">
                            <label for="timeline_edit_datetime">Data e hora</label>
                            <input name="datetime" id="timeline_edit_datetime" class="form-control timeline-datetime" type="text"
                                   value="{{ old('datetime', $item->getDateTime()) }}" required />
                        </div>
                        <div class="form-group">
                            <label for="timeline_edit_order">Ordem</label>
                            <input name="order" id="timeline_edit_order" class="form-control" type="number" min="1"
                                   value="{{ old('order', $item->order) }}" />
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_expected" value="1" @if(old('is_expected', $item->is_expected)) checked @endif>
                                Marcar como previsão
                            </label>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </section>
    </div>
@stop

@section('js')
    <script type="text/javascript" src="{{ url('/js/jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('.timeline-datetime').mask('00/00/0000 00:00');
        });
    </script>
@stop
