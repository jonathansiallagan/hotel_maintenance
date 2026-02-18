<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // 1. AUTO GENERATE UUID SAAT CREATE
    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->uuid)) {
                $asset->uuid = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'problem_history' => 'array',
    ];

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
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
