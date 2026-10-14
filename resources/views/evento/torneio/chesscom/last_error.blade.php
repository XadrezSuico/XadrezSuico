@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Torneio #'.$torneio->id.' - '.$pageTitle)

@section('content_header')
    <h1>Evento #{{ $evento->id }}: {{ $evento->name }} - Torneio #{{ $torneio->id }}: {{ $torneio->name }} &gt;&gt; {{ $pageTitle }}</h1>
@stop

@section('content')
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{ url('/evento/dashboard/'.$evento->id.'?tab=torneio') }}">Listar Todos os Torneios</a></li>
    </ul>

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{ $pageTitle }}</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width: 220px;">{{ $fieldLabels['action'] }}</th>
                        <td>{{ $actionLabel }}</td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['occurred_at'] }}</th>
                        <td>
                            @if(isset($error['occurred_at']) && is_numeric($error['occurred_at']))
                                {{ date('d/m/Y H:i:s', (int) $error['occurred_at']) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['user_id'] }}</th>
                        <td>{{ isset($error['user_id']) ? $error['user_id'] : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['message'] }}</th>
                        <td>{{ $errorMessage }}</td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['http_status'] }}</th>
                        <td>{{ isset($error['status_code']) ? $error['status_code'] : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['slug'] }}</th>
                        <td>{{ isset($error['slug']) ? $error['slug'] : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ $fieldLabels['request_url'] }}</th>
                        <td style="word-break: break-all;">{{ isset($error['request_url']) ? $error['request_url'] : '-' }}</td>
                    </tr>
                </tbody>
            </table>

            @if(!empty($previewImageUrl) || !empty($previewHtmlUrl))
                <h4>{{ $fieldLabels['response_preview'] }}</h4>
                @if(!empty($previewImageUrl))
                    <p>
                        <img src="{{ $previewImageUrl }}" alt="{{ $fieldLabels['response_preview'] }}" class="img-responsive" style="max-width: 100%; border: 1px solid #ddd;">
                    </p>
                @elseif(!empty($previewHtmlUrl))
                    <p class="text-muted">{{ $previewImageUnavailable }}</p>
                    <iframe src="{{ $previewHtmlUrl }}" title="{{ $fieldLabels['response_preview'] }}" style="width: 100%; height: 480px; border: 1px solid #ddd;"></iframe>
                @endif
            @endif

            <h4>{{ $fieldLabels['response_body'] }}</h4>
            <pre style="max-height: 480px; overflow: auto; white-space: pre-wrap; word-break: break-word;">{{ isset($error['response_body']) && $error['response_body'] !== '' ? $error['response_body'] : '-' }}</pre>
        </div>
    </div>
@endsection
