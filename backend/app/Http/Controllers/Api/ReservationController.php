<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Tampilkan daftar reservasi (bisa difilter berdasarkan status atau tanggal)
     */
    public function index(Request $request)
    {
        $query = Reservation::with('table')->orderBy('reserved_at', 'desc');

        // Filter berdasarkan status jika ada parameter ?status=pending
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter hari ini jika ada parameter ?today=true
        if ($request->has('today') && $request->today == 'true') {
            $query->whereDate('reserved_at', now()->toDateString());
        }

        return ReservationResource::collection($query->get());
    }

    /**
     * Tampilkan detail 1 reservasi
     */
    public function show(Reservation $reservation)
    {
        return new ReservationResource($reservation->load('table'));
    }

    /**
     * Update status reservasi (Konfirmasi DP, Kedatangan, atau Pembatalan)
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:confirmed,arrived,cancelled',
            // Jika dikonfirmasi, minta input metode pembayaran DP
            'payment_method' => 'required_if:status,confirmed|in:cash,qris,transfer'
        ]);

        DB::beginTransaction();
        try {
            // Jika status diubah menjadi confirmed (artinya DP sudah dibayar)
            if ($request->status === 'confirmed' && $reservation->status === 'pending') {
                $reservation->update([
                    'status' => 'confirmed',
                    'dp_status' => 'paid',
                    'payment_method' => $request->payment_method
                ]);
            }
            // Jika status diubah menjadi arrived (tamu datang)
            elseif ($request->status === 'arrived' && $reservation->status === 'confirmed') {
                $reservation->update([
                    'status' => 'arrived'
                ]);

                // Ubah status meja menjadi occupied (terisi)
                $reservation->table()->update(['status' => 'occupied']);
            }
            // Jika dibatalkan manual oleh admin
            elseif ($request->status === 'cancelled') {
                $reservation->update(['status' => 'cancelled']);
            }
            else {
                return response()->json(['message' => 'Transisi status tidak valid.'], 400);
            }

            DB::commit();
            return new ReservationResource($reservation->load('table'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }
}
