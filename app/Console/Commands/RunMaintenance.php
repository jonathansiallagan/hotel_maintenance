<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RunMaintenance extends Command
{
    protected $signature = 'maintenance:run';
    protected $description = 'Cek jadwal maintenance dan generate tiket otomatis';

    public function handle()
    {
        $today = Carbon::now()->startOfDay();

        $this->info("Memeriksa jadwal untuk: " . $today->toDateString());

        // 1. GANTI 'next_run' JADI 'next_due_date'
        $schedules = MaintenanceSchedule::where('is_active', true)
            ->whereDate('next_due_date', '<=', $today)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("Tidak ada jadwal hari ini.");
            return;
        }

        $count = 0;

        foreach ($schedules as $schedule) {
            // 2. CREATE TIKET (Sesuaikan description ambil dari title)
            Ticket::create([
                'user_id'       => 1,
                'asset_id'      => $schedule->asset_id,
                'title'         => $schedule->title,
                'ticket_number' => 'MNT-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'status'        => 'open',
                'priority'      => $schedule->priority,
                'description'   => "MAINTENANCE RUTIN: " . $schedule->title,
            ]);

            // 3. UPDATE TANGGAL BERIKUTNYA
            $schedule->last_performed_at = Carbon::now();

            $currentDueDate = Carbon::parse($schedule->next_due_date);

            switch ($schedule->frequency) {
                case 'weekly':
                    $schedule->next_due_date = $currentDueDate->addWeek();
                    break;
                case 'monthly':
                    $schedule->next_due_date = $currentDueDate->addMonth();
                    break;
                case 'quarterly':
                    $schedule->next_due_date = $currentDueDate->addMonths(3);
                    break;
                case 'yearly':
                    $schedule->next_due_date = $currentDueDate->addYear();
                    break;
            }

            $schedule->save();
            $count++;

            $this->info("Tiket dibuat: " . $schedule->title);
        }

        $this->info("Selesai! {$count} tiket berhasil dibuat.");
    }
}
