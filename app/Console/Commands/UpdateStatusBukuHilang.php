<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PinjamController;

class UpdateStatusBukuHilang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buku:update-hilang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update otomatis status buku yang tidak dikembalikan selama 1 tahun menjadi hilang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new PinjamController();
        $jumlah = $controller->updateStatusBukuHilangOtomatis();
        $this->info("Status hilang diupdate untuk $jumlah buku (jika ada)." );
    }
}
