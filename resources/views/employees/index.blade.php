@extends('layouts.app')

@section('content')
<h1 class="section-title">Zaměstnanci</h1>

<form method="POST" action="{{ route('employees.store') }}" class="form-row">
  @csrf
  <div class="field">
    <label>Jméno a příjmení</label>
    <input type="text" name="name" required>
  </div>
  <div class="field">
    <label>Pozice (volitelné)</label>
    <input type="text" name="position">
  </div>
  <div class="field">
    <label>PIN (volitelné, 4 číslice)</label>
    <input type="password" name="pin" inputmode="numeric" maxlength="4" placeholder="••••">
  </div>
  <button class="btn">Přidat zaměstnance</button>
</form>

@error('name')<p style="color:var(--danger);font-size:12.5px;margin-top:-12px;">{{ $message }}</p>@enderror
@error('pin')<p style="color:var(--danger);font-size:12.5px;margin-top:-12px;">{{ $message }}</p>@enderror

<div class="card">
  <table>
    <thead><tr><th>Jméno</th><th>Pozice</th><th style="width:260px;">PIN pro přihlášení</th><th></th></tr></thead>
    <tbody>
      @forelse($employees as $employee)
        <tr>
          <td>{{ $employee->name }}</td>
          <td>{{ $employee->position ?? '—' }}</td>
          <td>
            <form method="POST" action="{{ route('employees.pin', $employee) }}" style="display:flex;gap:6px;align-items:center;">
              @csrf
              <input type="password" name="pin" inputmode="numeric" maxlength="4" placeholder="{{ $employee->pin_hash ? 'nastaven — zadejte nový' : 'bez PIN' }}" style="width:110px;">
              <button class="btn ghost small">Uložit</button>
            </form>
          </td>
          <td>
            <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Opravdu odebrat tohoto zaměstnance? Historické záznamy zůstanou uložené.');">
              @csrf @method('DELETE')
              <button class="btn ghost small">Odebrat</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4"><div class="empty">Zatím žádní zaměstnanci. Přidejte prvního výše.</div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<p style="font-size:12px;color:var(--ink-soft);margin-top:16px;">
  PIN uložíte prázdným políčkem + tlačítkem "Uložit" odebráním se zaměstnanec vrátí k přihlášení jen jménem.
</p>
@endsection
