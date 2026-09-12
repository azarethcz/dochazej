@extends('layouts.app')

@section('content')
<h1 class="section-title">Dovolená</h1>

@unless(auth()->user()->isAdmin())
  <div class="card" style="margin-bottom:20px;">
    <form method="POST" action="{{ route('vacation.store') }}" class="form-row" style="margin-bottom:0;">
      @csrf
      <div class="field"><label>Od</label><input type="date" name="from_date" required></div>
      <div class="field"><label>Do</label><input type="date" name="to_date" required></div>
      <div class="field" style="flex:1;min-width:180px;"><label>Poznámka</label><input type="text" name="note" placeholder="volitelné"></div>
      <button class="btn">Požádat o dovolenou</button>
    </form>
    @error('to_date')<p style="color:var(--danger);font-size:12.5px;margin-top:10px;">{{ $message }}</p>@enderror
  </div>
@endunless

<div class="card">
  <table>
    <thead><tr><th>Zaměstnanec</th><th>Termín</th><th>Poznámka</th><th>Stav</th><th></th></tr></thead>
    <tbody>
      @forelse($requests as $req)
        <tr>
          <td>{{ $req->user->name }}</td>
          <td class="mono">{{ $req->from_date->format('d.m.Y') }} – {{ $req->to_date->format('d.m.Y') }}</td>
          <td>{{ $req->note ?? '—' }}</td>
          <td>
            @php $labels = ['pending' => ['čeká na schválení','pending'], 'approved' => ['schválena','approved'], 'rejected' => ['zamítnuta','rejected']]; @endphp
            <span class="status-pill pill-{{ $labels[$req->status][1] }}">{{ $labels[$req->status][0] }}</span>
          </td>
          <td>
            @if(auth()->user()->isAdmin() && $req->status === 'pending')
              <form method="POST" action="{{ route('vacation.approve', $req) }}" style="display:inline;">
                @csrf<button class="btn small">Schválit</button>
              </form>
              <form method="POST" action="{{ route('vacation.reject', $req) }}" style="display:inline;">
                @csrf<button class="btn ghost small">Zamítnout</button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="5"><div class="empty">Zatím žádné žádosti o dovolenou.</div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
