<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class MaintenanceSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'last_performed_at' => 'date',
        'next_due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Hitung tanggal berikutnya berdasarkan frekuensi
     * Dipanggil setelah Tiket Maintenance berhasil dibuat.
     */
    public function scheduleNextRun()
    {
        $currentDate = Carbon::parse($this->next_due_date);

        $nextDate = match ($this->frequency) {  
            'weekly'    => $currentDate->addWeek(),
            'monthly'   => $currentDate->addMonth(),
            'quarterly' => $currentDate->addMonths(3),
            'yearly'    => $currentDate->addYear(),
            default     => $currentDate->addMonth(),
        };

        $this->update([
            'last_performed_at' => now(),
            'next_due_date'     => $nextDate
        ]);
    }
}
