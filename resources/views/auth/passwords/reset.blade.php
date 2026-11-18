@extends('layouts.v2.account-form')
@section('title', 'Redefinir senha')
@section('account-title', 'Redefinir senha')
@section('account-content')
<p>Escolha uma nova senha para sua conta.</p>
<form method="post" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="v2-account-field">
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}" autocomplete="email" required autofocus @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
        @if($errors->has('email'))<div id="email-error" class="v2-account-error" role="alert">{{ $errors->first('email') }}</div>@endif
    </div>
    <div class="v2-account-field">
        <label for="password">Nova senha</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif>
        @if($errors->has('password'))<div id="password-error" class="v2-account-error" role="alert">{{ $errors->first('password') }}</div>@endif
    </div>
    <div class="v2-account-field">
        <label for="password_confirmation">Confirme a nova senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
    </div>
    <button type="submit">Redefinir senha</button>
</form>
@endsection
