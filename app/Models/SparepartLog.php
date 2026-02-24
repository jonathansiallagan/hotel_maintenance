<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sparepart() { return $this->belongsTo(Sparepart::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(Ticket::class); }
}