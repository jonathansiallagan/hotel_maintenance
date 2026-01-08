<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Tiket (Kebalikan dari Ticket model)
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_sparepart')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
