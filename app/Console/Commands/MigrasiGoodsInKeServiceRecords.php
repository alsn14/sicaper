<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrasiGoodsInKeServiceRecords extends Command
{
    protected $signature = 'migrasi:goodsin-service';
    protected $description = 'Migrasikan data dari tabel goods_in ke service_records';

    public function handle()
    {
        $services = DB::table('goods_in')->get();
        $dataValid = [];

        foreach ($services as $service) {
            // Cek data wajib
            if (!$service->item_id || !$service->date_received) {
                continue;
            }

            $dataValid[] = [
                'item_id' => $service->item_id,
                'tanggal_service' => $service->date_received,
                'biaya_service' => $service->biaya ?? null,
                'keterangan' => $service->keterangan ?? 'Migrasi dari goods_in',
            ];

            // Batasi preview ke 10 data
            //if (count($dataValid) >= 10) {
            //    break;
            // }
        }

        if (count($dataValid) === 0) {
            $this->warn("Tidak ada data valid ditemukan dari goods_in.");
            return;
        }

        // Tampilkan preview
        $this->info("Contoh data valid dari goods_in:");
        foreach ($dataValid as $i => $data) {
            $this->line(($i + 1) . '. item_id: ' . $data['item_id'] .
                        ', tanggal_service: ' . $data['tanggal_service'] .
                        ', biaya_service: ' . ($data['biaya_service'] ?? '-') .
                        ', keterangan: ' . $data['keterangan']);
        }

        // Konfirmasi migrasi
        if ($this->confirm("Lanjut migrasi semua data valid ke service_records?")) {
            $jumlah = 0;
            foreach ($services as $service) {
                if (!$service->item_id || !$service->date_received) {
                    continue;
                }
                $ada = DB::table('service_records')
                    ->where('item_id', $service->item_id)
                    ->where('tanggal_service', $service->date_received)
                    ->exists();

                if ($ada) continue;

                DB::table('service_records')->insert([
                    'item_id' => $service->item_id,
                    'tanggal_service' => $service->date_received,
                    'biaya_service' => $service->biaya ?? null,
                    'keterangan' => $service->jenis_pekerjaan ?? 'Migrasi dari goods_in',
                    'teknisi' => $service->teknisi ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $jumlah++;
            }

            $this->info("✅ Berhasil migrasi $jumlah data ke service_records.");
        } 
            else {
                $this->warn("❌ Migrasi dibatalkan.");
                }
        
    }

}
