<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>Docházej – {{ $employee->name }} – {{ $month }}</title>
<style>
  body{font-family:sans-serif;font-size:13px;color:#1B1A17;padding:24px;}
  h1{font-size:18px;margin-bottom:2px;}
  p{color:#555;margin-top:0;}
  table{width:100%;border-collapse:collapse;margin-top:16px;}
  th,td{text-align:left;padding:6px 8px;border-bottom:1px solid #ddd;font-size:12.5px;}
  tfoot td{font-weight:bold;background:#f0efe9;}
</style>
</head>
<body>
  <h1>Docházka – {{ $employee->name }}</h1>
  <p>{{ $employee->position }} · {{ $month }}</p>
  <table>
    <thead><tr><th>Den</th><th>Příchody / odchody</th><th>Přestávky (min)</th><th>Odpracováno</th></tr></thead>
    <tbody>
      @foreach($rows as $row)
        <tr>
          <td>{{ $row['date']->format('d.m.Y') }}</td>
          <td>
            @forelse($row['attendances'] as $a)
              {{ $a->clock_in->format('H:i') }}–{{ $a->clock_out?->format('H:i') ?? 'probíhá' }}<br>
            @empty
              —
            @endforelse
          </td>
          <td>{{ $row['breakMinutes'] ?: '—' }}</td>
          <td>{{ $row['workedMinutes'] ? intdiv($row['workedMinutes'],60).'h '.($row['workedMinutes']%60).'m' : '—' }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr><td colspan="3">Celkem za měsíc</td><td>{{ intdiv($monthTotalMinutes,60) }}h {{ $monthTotalMinutes%60 }}m</td></tr>
    </tfoot>
  </table>
</body>
</html>
