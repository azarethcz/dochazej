<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromArray, WithHeadings
{
    public function __construct(private User $employee, private Collection $rows)
    {
    }

    public function headings(): array
    {
        return ['Den', 'Den v týdnu', 'Příchody / odchody', 'Přestávky (min)', 'Odpracováno (min)'];
    }

    public function array(): array
    {
        $data = $this->rows->map(function ($row) {
            $sessions = $row['attendances']->map(function ($a) {
                $in = $a->clock_in->format('H:i');
                $out = $a->clock_out?->format('H:i') ?? 'probíhá';
                return "{$in}-{$out}";
            })->implode('; ');

            return [
                $row['date']->format('d.m.Y'),
                $row['date']->translatedFormat('l'),
                $sessions ?: '—',
                $row['breakMinutes'],
                $row['workedMinutes'],
            ];
        })->toArray();

        $data[] = ['', '', 'Celkem', $this->rows->sum('breakMinutes'), $this->rows->sum('workedMinutes')];

        return $data;
    }
}
