<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakPeriod extends Model
{
    protected $table = 'break_periods';

    protected $fillable = ['attendance_id', 'start', 'end'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
