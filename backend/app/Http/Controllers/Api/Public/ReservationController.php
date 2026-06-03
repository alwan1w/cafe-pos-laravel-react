<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use App\Http\Requests\PublicReservationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Cek meja yang kosong pada tanggal & jam tertentu
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'reserved_at' => 'required|date_format:Y-m-d H:i|after:now'
        ]);

        $requestedTime = Carbon::parse($request->reserved_at);

        // Buat rentang waktu aman (buffer 2 jam sebelum & sesudah)
        $bufferStart = $requestedTime->copy()->subHours(2);
        $bufferEnd = $requestedTime->copy()->addHours(2);

        // Cari ID meja yang sudah di-booking dan statusnya aktif
        $reservedTableIds = Reservation::whereIn('status', ['pending', 'confirmed', 'arrived'])
            ->whereBetween('reserved_at', [$bufferStart, $bufferEnd])
            ->pluck('table_id');

        // Ambil meja yang TIDAK ADA di dalam daftar ID di atas
        $availableTables = Table::whereNotIn('id', $reservedTableIds)
            ->where('status', '!=', 'occupied') // Jangan tampilkan meja yang sedang diduduki tamu walk-in
            ->get();

        return response()->json([
            'message' => 'Meja tersedia',
            'data' => $availableTables
        ]);
    }

    /**
     * Buat data reservasi baru
     */
    public function store(PublicReservationRequest $request)
    {
        $requestedTime = Carbon::parse($request->reserved_at);

        // Double check untuk mencegah Race Condition (2 orang booking bersamaan di detik yang sama)
        $bufferStart = $requestedTime->copy()->subHours(2);
        $bufferEnd = $requestedTime->copy()->addHours(2);

        $isBooked = Reservation::where('table_id', $request->table_id)
            ->whereIn('status', ['pending', 'confirmed', 'arrived'])
            ->whereBetween('reserved_at', [$bufferStart, $bufferEnd])
            ->exists();

        if ($isBooked) {
            return response()->json([
                'message' => 'Maaf, meja ini baru saja di-booking oleh orang lain pada rentang waktu tersebut.'
            ], 409); // 409 Conflict
        }

        // Simpan reservasi
        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'table_id' => $request->table_id,
                'guest_count' => $request->guest_count,
                'reserved_at' => $requestedTime,
                'expired_at' => $requestedTime->copy()->addMinutes(60), // Auto-expire 1 jam dari reserved_at sesuai PRD
                'notes' => $request->notes,
                'dp_amount' => 20000,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Reservasi berhasil dibuat. Silakan lakukan pembayaran DP.',
                'data' => $reservation
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat reservasi: ' . $e->getMessage()], 500);
        }
    }
}
