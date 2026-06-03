<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireReservationsCommand extends Command
{
    // Nama command yang akan dipanggil oleh scheduler
    protected $signature = 'reservations:expire';

    // Deskripsi command
    protected $description = 'Membatalkan reservasi yang sudah melewati batas waktu (expired_at) secara otomatis';

    public function handle()
    {
        $now = Carbon::now();

        // Cari reservasi yang waktunya sudah lewat dan belum kedatangan tamu
        $expiredReservations = Reservation::whereIn('status', ['pending', 'confirmed'])
            ->where('expired_at', '<', $now)
            ->get();

        $count = $expiredReservations->count();

        if ($count > 0) {
            // Update status menjadi expired
            Reservation::whereIn('id', $expiredReservations->pluck('id'))
                ->update(['status' => 'expired']);

            // Catat di log sistem agar Admin bisa melacak riwayatnya
            Log::info("Auto-Expire Cron: {$count} reservasi telah diubah menjadi expired.");
            $this->info("Berhasil meng-expire {$count} reservasi.");
        } else {
            $this->info("Tidak ada reservasi yang perlu di-expire saat ini.");
        }
    }
}
