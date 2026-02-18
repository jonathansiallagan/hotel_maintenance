<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default: Tanggal awal bulan ini s/d hari ini
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();
        $status = $request->status ?? 'all';

        // Query Tiket dengan Filter
        $tickets = Ticket::with(['user', 'asset', 'technician'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status != 'all') {
            $tickets->where('status', $status);
        }

        // Ambil data (tambah get())
        $tickets = $tickets->latest()->get();

        return view('Admin.reports.index', compact('tickets', 'startDate', 'endDate', 'status'));
    }

    // Fitur Cetak (Print View Sederhana)
    public function print(Request $request)
    {
        // Default: Tanggal awal bulan ini s/d hari ini
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();
        $status = $request->status ?? 'all';

        $tickets = Ticket::with(['user', 'asset', 'technician'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status != 'all') {
            $tickets->where('status', $status);
        }

        $tickets = $tickets->latest()->get();

        return view('Admin.reports.print', compact('tickets', 'startDate', 'endDate'));
    }
}
