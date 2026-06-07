<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature   = 'booking:cancel-expired';
    protected $description = 'Batalkan otomatis booking pending yang sudah melewati batas waktu pembayaran (24 jam)';

    public function handle(): void
    {
        Booking::cancelExpiredGracefully();
        $this->info('[booking:cancel-expired] Processed via cancelExpiredGracefully().');
    }
}
