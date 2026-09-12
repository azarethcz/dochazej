<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'clock_in', 'clock_out'];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breakPeriods(): HasMany
    {
        return $this->hasMany(BreakPeriod::class);
    }

    public function openBreak(): ?BreakPeriod
    {
        return $this->breakPeriods()->whereNull('end')->latest('start')->first();
    }

    public function breakMinutes(): int
    {
        return $this->breakPeriods->sum(function (BreakPeriod $break) {
            if (!$break->end) {
                return 0;
            }
            return $break->start->diffInMinutes($break->end);
        });
    }

    /**
     * Worked minutes excluding breaks. Null if the shift is still open.
     */
    public function workedMinutes(): ?int
    {
        if (!$this->clock_out) {
            return null;
        }
        return $this->clock_in->diffInMinutes($this->clock_out) - $this->breakMinutes();
    }
}
