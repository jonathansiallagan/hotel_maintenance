<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // MENAMPILKAN SEMUA TIKET (Monitoring)
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'asset', 'technician']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan judul, deskripsi, atau nama aset
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    // MENAMPILKAN DETAIL TIKET
    public function show($id)
    {
        $ticket = Ticket::with(['user', 'asset.location', 'technician', 'activities.user', 'spareparts.category'])
            ->findOrFail($id);

        return view('admin.tickets.show', compact('ticket'));
    }

    // CETAK TIKET (PDF/HTML untuk Print)
    public function print($id)
    {
        $ticket = Ticket::with(['user', 'asset.location', 'technician', 'spareparts'])
            ->findOrFail($id);

        return view('admin.tickets.print', compact('ticket'));
    }

    // UPDATE STATUS TIKET
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,pending_sparepart,resolved,closed',
            'technician_note' => 'nullable|string|max:1000'
        ]);

        $ticket = Ticket::findOrFail($id);

        // Cek jika tiket sudah resolved atau closed, tidak boleh diupdate lagi
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('error', 'Tiket yang sudah selesai tidak dapat diubah lagi.');
        }

        $oldStatus = $ticket->status;
        $newStatus = $request->status;

        // Update status dan catatan teknisi
        $ticket->update([
            'status' => $newStatus,
            'technician_notes' => $request->technician_note
        ]);

        // Set timestamps berdasarkan status
        if ($newStatus === 'resolved' && $oldStatus !== 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        } elseif ($newStatus === 'closed' && $oldStatus !== 'closed') {
            $ticket->update(['closed_at' => now()]);
        }

        // Log aktivitas
        $ticket->activities()->create([
            'user_id' => Auth::id(),
            'description' => "Status tiket diubah dari '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' menjadi '" . ucfirst(str_replace('_', ' ', $newStatus)) . "'"
        ]);

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    // HAPUS TIKET (Hanya Admin yang boleh)
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return back()->with('success', 'Tiket berhasil dihapus.');
    }

    // TAMBAH SPAREPART KE TIKET
    public function addSparepart(Request $request, $id)
    {
        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $ticket = Ticket::findOrFail($id);
        $sparepart = Sparepart::findOrFail($request->sparepart_id);

        // Cek jika tiket sudah resolved atau closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('error', 'Tidak dapat menambah sparepart ke tiket yang sudah selesai.');
        }

        // Cek stok tersedia
        if ($sparepart->stock < $request->quantity) {
            return back()->with('error', 'Stok sparepart tidak mencukupi. Stok tersedia: ' . $sparepart->stock);
        }

        // Cek apakah sparepart sudah ada di tiket ini
        $existingSparepart = $ticket->spareparts()->where('sparepart_id', $request->sparepart_id)->first();

        if ($existingSparepart) {
            // Cek stok untuk quantity tambahan
            if ($sparepart->stock < $request->quantity) {
                return back()->with('error', 'Stok sparepart tidak mencukupi untuk penambahan quantity. Stok tersedia: ' . $sparepart->stock);
            }

            // Update quantity jika sudah ada
            $newQuantity = $existingSparepart->pivot->quantity + $request->quantity;

            $ticket->spareparts()->updateExistingPivot($request->sparepart_id, [
                'quantity' => $newQuantity
            ]);
        } else {
            // Cek stok untuk quantity baru
            if ($sparepart->stock < $request->quantity) {
                return back()->with('error', 'Stok sparepart tidak mencukupi. Stok tersedia: ' . $sparepart->stock);
            }

            // Tambah sparepart baru
            $ticket->spareparts()->attach($request->sparepart_id, [
                'quantity' => $request->quantity
            ]);
        }

        // Kurangi stok sparepart
        $sparepart->decrement('stock', $request->quantity);

        // Log aktivitas
        $ticket->activities()->create([
            'user_id' => Auth::id(),
            'description' => "Sparepart '{$sparepart->name}' ({$request->quantity} unit) ditambahkan ke tiket"
        ]);

        return back()->with('success', 'Sparepart berhasil ditambahkan ke tiket.');
    }

    // HAPUS SPAREPART DARI TIKET
    public function removeSparepart($ticketId, $sparepartId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $sparepart = Sparepart::findOrFail($sparepartId);

        // Cek jika tiket sudah resolved atau closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('error', 'Tidak dapat menghapus sparepart dari tiket yang sudah selesai.');
        }

        // Cari sparepart di tiket
        $ticketSparepart = $ticket->spareparts()->where('sparepart_id', $sparepartId)->first();

        if (!$ticketSparepart) {
            return back()->with('error', 'Sparepart tidak ditemukan di tiket ini.');
        }

        $quantity = $ticketSparepart->pivot->quantity;

        // Kembalikan stok sparepart
        $sparepart->increment('stock', $quantity);

        // Hapus dari tiket
        $ticket->spareparts()->detach($sparepartId);

        // Log aktivitas
        $ticket->activities()->create([
            'user_id' => Auth::id(),
            'description' => "Sparepart '{$sparepart->name}' ({$quantity} unit) dihapus dari tiket"
        ]);

        return back()->with('success', 'Sparepart berhasil dihapus dari tiket.');
    }
}
