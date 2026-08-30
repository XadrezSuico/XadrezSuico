@if ($errors->any())
    @if(!$errors->has('alerta'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
            <h4 class="mb-2 font-semibold">Opa! Deu erro!</h4>
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        @php $alertType = $errors->first('type'); @endphp
        <div class="mb-6 rounded-lg border p-4 {{ $alertType === 'success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' }}" role="alert">
            @if($alertType == 'success')
                <strong class="font-semibold">Tudo certo!</strong>
            @else
                <strong class="font-semibold">Opa! Deu erro!</strong>
            @endif
            <br>
            {{ $errors->first('alerta') }}
        </div>
    @endif
@endif
