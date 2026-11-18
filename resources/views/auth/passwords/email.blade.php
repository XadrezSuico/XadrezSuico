@extends('layouts.v2.account-form')
@section('title', 'Recuperar senha')
@section('account-title', 'Recuperar senha')
@section('account-content')
<p>Informe o e-mail da sua conta para receber o link de redefinição de senha.</p>
<form method="post" action="{{ route('password.email') }}">
    @csrf
    <div class="v2-account-field">
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
        @if($errors->has('email'))<div id="email-error" class="v2-account-error" role="alert">{{ $errors->first('email') }}</div>@endif
    </div>
    <button type="submit">Enviar link de recuperação</button>
</form>
@endsection
