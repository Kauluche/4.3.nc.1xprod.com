<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'latitude',
        'longitude',
        'address',
        'city',
        'postal_code',
        'country',
        'status',
        'commercial_id',
        'zone_id',
        'monthly_goal',
        'finess_id',
    ];

    protected $casts = [
        'monthly_goal' => 'decimal:2',
    ];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}