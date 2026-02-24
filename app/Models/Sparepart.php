<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetCategory;
use App\Models\SparepartCategory;

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

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(SparepartCategory::class, 'sparepart_category_id');
    }

    // Relasi ke SparepartLog
    public function logs()
    {
        return $this->hasMany(SparepartLog::class)->latest();
    }
}
