<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'mac_address',
        'name',
        'device_type',
        'is_active',
        'metadata',
        'last_seen_at',
        'person_in_charge_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public static function findByMacAddress(string $macAddress)
    {
        return static::where('mac_address', $macAddress)->first();
    }

    public function updateLastSeen()
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function personInCharge(): BelongsTo
    {
        return $this->belongsTo(PersonInCharge::class);
    }
} 