<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Models\Sparepart;

class SearchController extends Controller
{
    private function getPageNumber($modelClass, $itemId, $perPage = 10)
    {
        $position = $modelClass::where('created_at', '>', function ($query) use ($modelClass, $itemId) {
            $query->select('created_at')->from((new $modelClass)->getTable())->where('id', $itemId);
        })->count();

        return floor($position / $perPage) + 1;
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $limit = 5;
        $perPage = 10;

        // 1. CARI TIKET (Ke Halaman Detail)
        try {
            $tickets = Ticket::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('ticket_number', 'like', "%{$query}%")
                ->orWhere('id', 'like', "%{$query}%")
                ->latest()->take($limit)->get();

            foreach ($tickets as $ticket) {
                $assetName = $ticket->asset->name ?? 'Tanpa Aset';
                $displayTitle = $assetName . ' - ' . $ticket->title;

                $results[] = [
                    'id' => 'ticket-' . $ticket->id,    
                    'title' => $displayTitle,
                    'url' => route('Admin.tickets.show', $ticket->id),
                    'type' => 'Tiket',
                    'status' => $ticket->ticket_number,
                ];
            }
        } catch (\Exception $e) {
        }

        // 2. CARI ASET (Ke Halaman Detail) - cari berdasarkan nama atau serial_number
        try {
            $assets = Asset::where('name', 'like', "%{$query}%")
                ->orWhere('serial_number', 'like', "%{$query}%")
                ->take($limit)->get();

            foreach ($assets as $asset) {
                $results[] = [
                    'id' => 'asset-' . $asset->id,
                    'title' => $asset->name,
                    'url' => route('Admin.assets.show', $asset->id) . '#asset-' . $asset->id,
                    'type' => 'Aset',
                    'status' => $asset->serial_number ?? ($asset->status ?? 'No S/N'),
                ];
            }
        } catch (\Exception $e) {
        }

        // 3. CARI SPAREPART (Ke Halaman Index + Pagination) - cari berdasarkan nama atau sku_code
        try {
            $spareparts = Sparepart::where('name', 'like', "%{$query}%")
                ->orWhere('sku_code', 'like', "%{$query}%")
                ->take($limit)->get();

            foreach ($spareparts as $part) {
                $page = $this->getPageNumber(Sparepart::class, $part->id, $perPage);

                $results[] = [
                    'id' => 'part-' . $part->id,
                    'title' => $part->name,
                    'url' => route('Admin.spareparts.index', ['page' => $page]) . '#part-' . $part->id,
                    'type' => 'Sparepart',
                    'status' => 'SKU: ' . ($part->sku_code ?? '-') . ' • Stok: ' . ($part->stock ?? 0),
                ];
            }
        } catch (\Exception $e) {
        }

        // 4. CARI USER (Ke Halaman Index + Pagination)
        try {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")->take($limit)->get();

            foreach ($users as $user) {
                $page = $this->getPageNumber(User::class, $user->id, $perPage);
                $roleName = $user->role ?? 'User';

                $results[] = [
                    'id' => 'user-' . $user->id,
                    'title' => $user->name,
                    'url' => route('Admin.users.index', ['page' => $page]) . '#user-' . $user->id,
                    'type' => 'Pengguna',
                    'status' => ucfirst($roleName),
                ];
            }
        } catch (\Exception $e) {
        }

        return response()->json(['results' => $results]);
    }
}
