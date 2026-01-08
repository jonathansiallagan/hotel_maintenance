<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // Casting tanggal agar otomatis jadi object Carbon (mudah diformat tgl/jam)
    protected $casts = [
        'reported_at' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_deadline' => 'datetime',
    ];

    // Relasi: Dimiliki oleh Aset apa?
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi: Siapa pelapornya?
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // Relasi: Siapa teknisinya?
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // Relasi: Punya banyak log aktivitas
    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    // Relasi: Menggunakan banyak Sparepart (Many-to-Many)
    public function spareparts()
    {
        return $this->belongsToMany(Sparepart::class, 'ticket_sparepart')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
