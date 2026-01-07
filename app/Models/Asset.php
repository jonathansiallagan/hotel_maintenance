<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke Lokasi
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Relasi ke Tiket (History Kerusakan)
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Relasi ke Jadwal Maintenance
    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}