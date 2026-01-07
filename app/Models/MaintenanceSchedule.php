<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['last_performed_at' => 'date', 'next_due_date' => 'date', 'is_active' => 'boolean'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
