@extends('layouts.app')

@section('content')
<h1 class="section-title">Záznamy docházky</h1>

<form method="GET" action="{{ route('records.index') }}" class="form-row">
  @if(auth()->user()->isAdmin())
    <div class="field">
      <label>Zaměstnanec</label>
      <select name="employee_id" onchange="this.form.submit()">
        @foreach($employees as $employee)
          <option value="{{ $employee->id }}" {{ $selectedEmployeeId == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
        @endforeach
      </select>
    </div>
  @endif
  <div class="field">
    <label>Měsíc</label>
    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()">
  </div>
  <a class="btn ghost small" href="{{ route('records.export.xlsx', ['employee_id' => $selectedEmployeeId, 'month' => $month]) }}">Export XLSX</a>
  <a class="btn ghost small" target="_blank" href="{{ route('records.export.pdf', ['employee_id' => $selectedEmployeeId, 'month' => $month]) }}">Export PDF</a>
</form>

<div class="card">
  <table>
    <thead><tr><th style="width:70px;">Den</th><th>Příchody / odchody</th><th style="width:90px;">Přestávky</th><th style="width:100px;">Odpracováno</th></tr></thead>
    <tbody>
      @forelse($rows as $row)
        <tr>
          <td class="mono">{{ $row['date']->format('d') }}<br><span style="color:var(--ink-soft);font-size:11px;">{{ $row['date']->translatedFormat('D') }}</span></td>
          <td>
            @forelse($row['attendances'] as $a)
              <div class="mono" style="{{ !$a->clock_out ? 'color:var(--accent);' : '' }}">
                {{ $a->clock_in->format('H:i') }} – {{ $a->clock_out?->format('H:i') ?? 'probíhá' }}
              </div>
            @empty
              <span style="color:var(--ink-soft);">—</span>
            @endforelse
          </td>
          <td class="mono">{{ $row['breakMinutes'] ? intdiv($row['breakMinutes'],60).':'.str_pad($row['breakMinutes']%60,2,'0',STR_PAD_LEFT) : '—' }}</td>
          <td class="mono">{{ $row['workedMinutes'] ? intdiv($row['workedMinutes'],60).':'.str_pad($row['workedMinutes']%60,2,'0',STR_PAD_LEFT) : '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="4"><div class="empty">Vyberte zaměstnance a měsíc.</div></td></tr>
      @endforelse
      @if($rows->isNotEmpty())
        <tr class="total-row">
          <td></td><td>Celkem za měsíc</td>
          <td class="mono">{{ intdiv($monthBreakMinutes,60) }}:{{ str_pad($monthBreakMinutes%60,2,'0',STR_PAD_LEFT) }}</td>
          <td class="mono">{{ intdiv($monthTotalMinutes,60) }}:{{ str_pad($monthTotalMinutes%60,2,'0',STR_PAD_LEFT) }}</td>
        </tr>
      @endif
    </tbody>
  </table>
</div>
<p style="font-size:12px;color:var(--ink-soft);margin-top:24px;">
  Export XLSX vyžaduje balíček <code>maatwebsite/excel</code>, export PDF balíček <code>barryvdh/laravel-dompdf</code>
  (composer require maatwebsite/excel barryvdh/laravel-dompdf).
</p>
@endsection
