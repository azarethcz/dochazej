@extends('layouts.app')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:28px;flex-wrap:wrap;gap:8px;">
  <h1 class="section-title" style="margin:0;">{{ now()->translatedFormat('l, j. F Y') }}</h1>
  <span style="color:var(--ink-soft);font-size:13px;">{{ $clockedIn }} z {{ $employees->count() }} právě přítomno</span>
</div>

@if($employees->isEmpty())
  <div class="empty">Zatím žádní zaměstnanci. Přidejte je v záložce "Zaměstnanci".</div>
@else
  <div class="grid">
    @foreach($employees as $employee)
      @php
        $attendance = $employee->attendances->firstWhere('clock_out', null) ?? $employee->attendances->last();
        $isOpen = $attendance && !$attendance->clock_out;
        $openBreak = $isOpen ? $attendance->openBreak() : null;
      @endphp
      <div class="emp-card">
        <div>
          <div class="emp-name">{{ $employee->name }}</div>
          @if($employee->position)<div class="emp-pos">{{ $employee->position }}</div>@endif
        </div>

        @if($isOpen && $openBreak)
          <span class="status-badge status-break"><span class="dot"></span>Přestávka od {{ $openBreak->start->format('H:i') }}</span>
        @elseif($isOpen)
          <span class="status-badge status-in"><span class="dot"></span>V práci od {{ $attendance->clock_in->format('H:i') }}</span>
        @else
          <span class="status-badge status-out"><span class="dot"></span>{{ $attendance ? 'Ukončeno '.$attendance->clock_out->format('H:i') : 'Nepřítomen' }}</span>
        @endif

        <div class="card-actions">
          @if($isOpen && $openBreak)
            <form method="POST" action="{{ route('clock.breakEnd') }}">
              @csrf<input type="hidden" name="user_id" value="{{ $employee->id }}">
              <button class="btn break-btn small">Konec přestávky</button>
            </form>
            <form method="POST" action="{{ route('clock.out') }}">
              @csrf<input type="hidden" name="user_id" value="{{ $employee->id }}">
              <button class="btn clock-out small">Odchod</button>
            </form>
          @elseif($isOpen)
            <form method="POST" action="{{ route('clock.breakStart') }}">
              @csrf<input type="hidden" name="user_id" value="{{ $employee->id }}">
              <button class="btn break-btn small">Přestávka</button>
            </form>
            <form method="POST" action="{{ route('clock.out') }}">
              @csrf<input type="hidden" name="user_id" value="{{ $employee->id }}">
              <button class="btn clock-out small">Odchod</button>
            </form>
          @else
            <form method="POST" action="{{ route('clock.in') }}">
              @csrf<input type="hidden" name="user_id" value="{{ $employee->id }}">
              <button class="btn clock-in small">Příchod</button>
            </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>
@endif
@endsection
