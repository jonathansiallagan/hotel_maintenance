<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceSchedule;
use App\Models\Asset;

class MaintenanceController extends Controller
{
    public function index()
    {
        $schedules = MaintenanceSchedule::with('asset')->latest()->paginate(10);
        $assets = Asset::all();
        return view('Admin.maintenance.index', compact('schedules', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string',
            'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        MaintenanceSchedule::create([
            'asset_id' => $request->asset_id,
            'title' => $request->title,
            'frequency' => $request->frequency,
            'priority' => $request->priority,
            'next_due_date' => $request->start_date,
            'is_active' => true,
        ]);

        return back()->with('success', 'Jadwal rutin berhasil dibuat.');
    }
}
