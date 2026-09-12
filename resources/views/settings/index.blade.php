@extends('layouts.app')

@section('content')
<h1 class="section-title">Nastavení</h1>
<div class="card" style="max-width:360px;">
  <form method="POST" action="{{ route('settings.password') }}">
    @csrf
    <div class="field" style="margin-bottom:14px;">
      <label>Nové heslo administrátora</label>
      <input type="password" name="password" required>
    </div>
    <div class="field" style="margin-bottom:14px;">
      <label>Potvrzení hesla</label>
      <input type="password" name="password_confirmation" required>
    </div>
    <button class="btn">Uložit heslo</button>
  </form>
  @error('password')<p style="color:var(--danger);font-size:12.5px;margin-top:10px;">{{ $message }}</p>@enderror
</div>
@endsection
