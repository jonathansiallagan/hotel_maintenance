<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // Konstanta Role untuk kemudahan coding
    const ROLE_ADMIN = 'admin_manager';
    const ROLE_TEKNISI = 'technician';
    const ROLE_USER = 'user_reporter';

    protected $guarded = ['id']; // Semua kolom boleh diisi kecuali ID

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi: Tiket yang DILAPORKAN oleh user ini
    public function reportedTickets()
    {
        return $this->hasMany(Ticket::class, 'reporter_id');
    }

    // Relasi: Tiket yang DIKERJAKAN oleh teknisi ini
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'technician_id');
    }
}
