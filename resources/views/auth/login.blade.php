<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Přihlášení – Docházej</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=IBM+Plex+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{ --bg:#EDEBE3; --surface:#FAF9F5; --ink:#1B1A17; --ink-soft:#6E6C62; --line:#D9D6C9; --accent:#1F5C52; --danger:#A6403A; --radius:4px; }
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:'IBM Plex Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .login-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:36px;width:340px;}
  .brand-row{display:flex;align-items:center;gap:10px;margin-bottom:4px;}
  .brand-row img{width:34px;height:34px;}
  .login-title{font-family:'Fraunces',serif;font-size:23px;margin:0;}
  .login-sub{color:var(--ink-soft);font-size:12.5px;margin:0 0 24px;}
  .role-toggle{display:flex;gap:8px;margin-bottom:18px;}
  .role-toggle a{flex:1;padding:9px;border:1px solid var(--line);background:#fff;border-radius:var(--radius);color:var(--ink-soft);font-size:13px;text-align:center;text-decoration:none;}
  .role-toggle a.active{background:var(--ink);color:#fff;border-color:var(--ink);}
  .field{margin-bottom:14px;}
  .field label{display:block;font-size:12px;color:var(--ink-soft);margin-bottom:5px;}
  .field select,.field input{width:100%;font-family:inherit;font-size:14px;padding:9px 10px;border:1px solid var(--line);border-radius:var(--radius);}
  .btn{width:100%;border:none;background:var(--ink);color:#fff;padding:10px;border-radius:var(--radius);font-size:14px;cursor:pointer;}
  .error{color:var(--danger);font-size:12.5px;margin-bottom:10px;}
</style>
</head>
<body>
  <div class="login-card">
    <div class="brand-row">
      <img src="{{ asset('images/logo.svg') }}" alt="Docházej">
      <p class="login-title">Docházej</p>
    </div>
    <p class="login-sub">Přihlaste se pro pokračování</p>

    <div class="role-toggle">
      <a href="{{ route('login') }}" class="{{ request('as') !== 'admin' ? 'active' : '' }}">Zaměstnanec</a>
      <a href="{{ route('login', ['as' => 'admin']) }}" class="{{ request('as') === 'admin' ? 'active' : '' }}">Administrátor</a>
    </div>

    @if($errors->any())
      <p class="error">{{ $errors->first() }}</p>
    @endif

    @if(request('as') === 'admin')
      <form method="POST" action="{{ route('login.admin') }}">
        @csrf
        <div class="field">
          <label>E-mail</label>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
          <label>Heslo</label>
          <input type="password" name="password" required>
        </div>
        <button class="btn">Přihlásit se</button>
      </form>
    @else
      @if($employees->isEmpty())
        <p class="error">Zatím žádní zaměstnanci. Přihlaste se jako administrátor a přidejte je.</p>
      @else
        <form method="POST" action="{{ route('login.employee') }}">
          @csrf
          <div class="field">
            <label>Vyberte jméno</label>
            <select name="user_id" required>
              @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>PIN (pokud je pro vás nastaven)</label>
            <input type="password" name="pin" inputmode="numeric" maxlength="4" placeholder="••••">
          </div>
          <button class="btn">Přihlásit se</button>
        </form>
      @endif
    @endif
  </div>
</body>
</html>
