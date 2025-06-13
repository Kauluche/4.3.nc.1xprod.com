<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'zone_id',
        'birth_date',
        'gender',
        'phone',
        'address',
        'city',
        'postal_code',
        'hire_date',
        'department',
        'position',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'two_factor_enabled' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function pharmacies()
    {
        return $this->hasMany(Pharmacy::class, 'commercial_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'commercial_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'commercial_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCommercial()
    {
        return $this->role === 'commercial';
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
