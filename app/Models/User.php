<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'position',
        'email',
        'password',
	'pin_hash',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function vacationRequests(): HasMany
    {
        return $this->hasMany(VacationRequest::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * The attendance record for "right now" that has not been clocked out yet, if any.
     */
    public function openAttendance(): ?Attendance
    {
        return $this->attendances()->whereNull('clock_out')->latest('clock_in')->first();
    }
}
