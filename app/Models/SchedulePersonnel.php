<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchedulePersonnel extends Model
{
    protected $table = 'schedule_personnel';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'name',
        'payment',
        'is_paid',
        'role_in_schedule',
        'notes',
    ];

    protected $casts = [
        'payment' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function getPersonnelNameAttribute(): string
    {
        return $this->name ?: ($this->user ? $this->user->name : 'Unassigned Staff');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
