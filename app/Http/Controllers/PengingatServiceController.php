<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengingatService;
use Carbon\Carbon;
use App\Models\GoodsIn;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Schema;
use App\Helpers\ServiceHelper; // Pastikan ini sesuai dengan namespace yang benar


class PengingatServiceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $services = GoodsIn::all(); // ambil semua data service

        foreach ($services as $service) {
            $existingReminder = PengingatService::where('item_id', $service->item_id)->first();
        
            if (!$existingReminder) {
                PengingatService::create([
                    'item_id' => $service->item_id,
                    'date_received' => $service->date_received,
                    'status' => 'Pending',
                    'jadwal_service' => Carbon::parse($service->date_received)->addMonths(6),
                ]);
            }
        }

        $barang = PengingatService::with('item.category')
        ->orderBy('date_received', 'asc')
        ->where('status', '!=', 'selesai')
        ->get()
        ->filter(function ($item) use ($today) {
            $jenis = $item->item->category->name ?? null;

            if ($jenis == 'Komputer') {
                return Carbon::parse($item->date_received)->diffInMonths($today) >= 6;
            } elseif ($jenis == 'Perabotan Kayu') {
                return Carbon::parse($item->date_received)->diffInMonths($today) >= 6;
            } elseif ($jenis == 'Printer') {
                return Carbon::parse($item->date_received)->diffInMonths($today) >= 4;
            } else {
                return Carbon::parse($item->date_received)->diffInMonths($today) >= 6;
            }
        });

        // Tambahkan rekomendasi service berdasarkan jenis barang
        $barang = PengingatService::all();
        $jenisbarang = Category::all();
        return view('service.pengingat', compact('barang', 'jenisbarang'));
    }
    public function daftarPengingat()
    {
        $barang = PengingatService::all();
        $jenisbarang = Category::all(); // ⬅️ WAJIB ditambahkan agar $jenisbarang tersedia di blade
        return view('service.pengingat', compact('barang', 'jenisbarang'));
    }

    public function tandaiSudahDiservice($id)
    {
        $pengingat = PengingatService::findOrFail($id);

        $pengingat->date_received = Carbon::now(); // update ke tanggal sekarang
        $pengingat->status = 'selesai';
        $pengingat->save();

        return redirect()->back()->with('success', 'Barang telah ditandai sudah diservice.');
    }

    public function prediksiService(Request $request)
    {
        $jenis = escapeshellarg($request->input('jenis'));
        $tanggalPembelian = escapeshellarg($request->input('tanggal_pembelian'));
        $tanggalServiceTerakhir = escapeshellarg($request->input('tanggal_service_terakhir'));

        $scriptPath = base_path('ai/regresi.py');
        $command = "python3 $scriptPath $jenis $tanggalPembelian $tanggalServiceTerakhir";

        $output = shell_exec($command);

        return response()->json([
            'hasil_prediksi_hari' => intval(trim($output))
        ]);
    }
    public function generateDariAI()
    {
        //$csvPath = storage_path('app/public/data_service.csv');
        $csvPath = $this->exportDataBarangToCsv();
        $scriptPath = base_path('ai/regresi_service.py');

        // Jalankan skrip Python
        $output = shell_exec("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($csvPath));

        // Cek jika output kosong atau null
        if (!$output) {
            return redirect()->back()->with('error', 'Gagal menjalankan skrip Python atau tidak ada output.');
        }

        // Parsing output JSON dari Python
        $hasil = json_decode($output, true);

        if (!is_array($hasil)) {
            return redirect()->back()->with('error', 'Output dari Python tidak dapat diparsing sebagai JSON.');
        }

        // Simpan hasil ke database
        foreach ($hasil as $data) {
            $namaBarang = $data['nama_barang'];
            $jenisBarang = $data['jenis_barang'];
            $tanggalPrediksi = Carbon::parse($data['prediksi_service_berikutnya']);

            // Cari item berdasarkan nama barang (pastikan nama barang unik atau gunakan metode yang lebih akurat)
            $item = Item::where('name', $namaBarang)->first();

            if ($item) {
                PengingatService::updateOrCreate(
                    ['item_id' => $item->id],
                    [
                        'date_received' => $item->goodsIn->date_received ?? Carbon::now(),
                        'jadwal_service' => $tanggalPrediksi,
                        'status' => 'Pending',
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Data hasil prediksi berhasil disimpan ke pengingat service.');
    }

    private function sinkronisasiTanggalServiceTerakhir()
    {
        $items = Item::all();

        foreach ($items as $item) {
            $latest = ServiceRecord::where('item_id', $item->id)
                        ->orderByDesc('tanggal_service')
                        ->first();

            $item->tanggal_service_terakhir = $latest ? $latest->tanggal_service : null;
            $item->save();
        }

        Log::info("✅ Sinkronisasi tanggal_service_terakhir selesai.");
    }

    private function sinkronisasiServiceRecordsDariGoodsIn()
    {
        // Ambil semua data goods_in
        $goodsInData = DB::table('goods_in')->get();

        // Buat array unik dari pasangan (item_id, date_received)
        $goodsInKeys = $goodsInData->map(function ($item) {
            return $item->item_id . '|' . $item->date_received;
        })->toArray();

        // Hapus service_records yang tidak ada di goods_in
        $allServiceRecords = DB::table('service_records')->get();
        foreach ($allServiceRecords as $record) {
            $key = $record->item_id . '|' . $record->tanggal_service;
            if (!in_array($key, $goodsInKeys)) {
                DB::table('service_records')
                    ->where('item_id', $record->item_id)
                    ->where('tanggal_service', $record->tanggal_service)
                    ->delete();
            }
        }

        // Update or insert dari goods_in
        foreach ($goodsInData as $entry) {
            DB::table('service_records')->updateOrInsert(
                [
                    'item_id' => $entry->item_id,
                    'tanggal_service' => $entry->date_received,
                ],
                [
                    'biaya_service' => $entry->biaya ?? 0,
                    'teknisi' => $entry->teknisi ?? 'Tidak diketahui',
                    'keterangan' => $entry->keterangan ?? '-',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Log::info('✅ Sinkronisasi service_records dari goods_in selesai.');
    }
    
    private function exportDataBarangToCsv()
    {
        // Ambil semua data barang beserta kategorinya
       $data = DB::table('items')
        ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
        ->leftJoin(DB::raw('(SELECT item_id, MAX(date_received) as tanggal_service_terakhir FROM goods_in GROUP BY item_id) as gi'), 'items.id', '=', 'gi.item_id')
        ->select(
            'items.id as id_barang',
            'items.name as nama_barang',
            'categories.name as jenis_barang',
            'items.tanggal_pembelian',
            DB::raw('COALESCE(gi.tanggal_service_terakhir, items.tanggal_pembelian) as tanggal_service_terakhir')
            //'gi.tanggal_service_terakhir'
        )
        ->get();

        // Siapkan header CSV
        $csvData = "id_barang,nama_barang,jenis_barang,tanggal_pembelian,tanggal_service_terakhir\n";

        // Loop tiap barang dan tambah ke CSV
        foreach ($data as $item) {
            $csvData .= "\"{$item->id_barang}\",\"{$item->nama_barang}\",\"{$item->jenis_barang}\",\"{$item->tanggal_pembelian}\",\"{$item->tanggal_service_terakhir}\"\n";
        }

        // Tentukan path file CSV
        $path = base_path('ai/data_service.csv');

        // Simpan ke file
        file_put_contents($path, $csvData);

        return $path; // Kirim balik path ke file
    }

    private function exportServiceRecordsToCsv()
    {
         $data = DB::table('service_records')
            ->leftJoin('items', 'service_records.item_id', '=', 'items.id')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->select(
                'items.id as id_barang',
                'items.name as nama_barang',
                'categories.name as jenis_barang', // Tambahkan kolom jenis barang
                'service_records.tanggal_service',
                'service_records.biaya_service',
                'service_records.teknisi',
                'service_records.keterangan'
            )
            ->orderBy('items.id')
            ->orderBy('service_records.tanggal_service')
            ->get();

        // Siapkan header CSV
        $csvData = "id_barang,nama_barang,jenis_barang,tanggal_service,biaya_service,teknisi,keterangan\n";

        // Loop tiap data
        foreach ($data as $record) {
        $csvData .= "\"{$record->id_barang}\",\"{$record->nama_barang}\",\"{$record->jenis_barang}\",\"{$record->tanggal_service}\",\"{$record->biaya_service}\",\"{$record->teknisi}\",\"{$record->keterangan}\"\n";
        }

        // Simpan ke file di folder ai/
        $path = base_path('ai/service_records_export.csv');
        file_put_contents($path, $csvData);

        return $path;
    }

    

    public function generatePrediksiService()
    {
        Log::info("⚠️ FUNGSI generatePrediksiService() DIPANGGIL!");

        // 🔁 Tambahkan sinkronisasi service records dari goods_in
        $this->sinkronisasiServiceRecordsDariGoodsIn();

        // 1. Export data ke CSV
        $this->exportDataBarangToCsv();
        $this->exportServiceRecordsToCsv();
        // ✅ 0. Sinkronisasi dulu tanggal terakhir service
        $this->sinkronisasiTanggalServiceTerakhir();

        // 2. Jalankan Python dan ambil hasil prediksi
        $python = 'C:/laragon/bin/python/python-3.10/python.exe'; // Sesuaikan path
        $scriptPath = base_path('ai/regresi_service.py');
        $output = shell_exec("\"{$python}\" \"{$scriptPath}\" 2>&1");

        Log::info("HASIL PYTHON:", [$output]);

        // Tambahan: Jalankan rekomendasi_service.py setelah regresi
        $python = 'C:/laragon/bin/python/python-3.10/python.exe';
        $rekomendasiScript = base_path('ai/rekomendasi_service.py');
        // Jalankan python dari folder ai/
        $process = proc_open(
            "\"$python\" rekomendasi_service.py",
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ],
            $pipes,
            base_path('ai') // <--- ini penting: jalankan dari folder ai
        );

        $outputRekomendasi = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);

        // Logging
        Log::info("HASIL REKOMENDASI PYTHON:", [$outputRekomendasi]);
        Log::error("PYTHON ERROR:", [$errorOutput]);

        // 3. Decode hasil output JSON dari Python
        $start = strpos($output, '[{');
        $end = strrpos($output, '}]');

        if ($start !== false && $end !== false) {
            $jsonPart = substr($output, $start, $end - $start + 2);
            $predictions = json_decode($jsonPart, true);
        } else {
            $predictions = [];
        }

        // Log::info("🧪 Ekstrak manual JSON berhasil", ['total' => count($predictions)]);

        // 4. Ambil semua ID barang dari hasil prediksi
        $ids = collect($predictions)->pluck('id_barang')->all();

        // 5. Hapus data prediksi lama
        // Log::info("Barang yang akan dihapus dari pengingat_services:", $ids);
        DB::table('pengingat_services')->whereIn('item_id', $ids)->delete();

        // 6. Masukkan data prediksi baru
        foreach ($predictions as $prediksi) {
            if (!empty($prediksi['id_barang'])) {
                $item = Item::find($prediksi['id_barang']);
                if (!$item) {
                    Log::error("Jenis barang tidak ditemukan untuk ID barang: " . $prediksi['id_barang']);
                    continue;
                }

                if (empty($prediksi['tanggal_service_terakhir'])) {
                    Log::info("Barang belum pernah diservice: " . $prediksi['nama_barang']);
                }

                $jadwal = $prediksi['prediksi_jadwal_service'] ?? null;

                //Log::info("Prediksi jadwal untuk {$item->name}: {$jadwal}");

                DB::table('pengingat_services')->insert([
                    'item_id' => $prediksi['id_barang'],
                    'jadwal_service' => $jadwal,
                    'date_received' => $prediksi['tanggal_service_terakhir'],
                    'status' => 'Pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        //Log::info("DATA DISIMPAN:", ["Jumlah data: " . count($predictions)]);

        return redirect()->back()->with('success', 'Prediksi service berhasil disimpan.');
    }
        

    // Menampilkan data prediksi service regresi
    // 
    public function tampilkanPrediksi()
    {
        // 1. Ambil data dari database
        $data = DB::table('pengingat_services')
            ->join('items', 'pengingat_services.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->select(
                'items.id as item_id',
                'items.name as nama_barang',
                'categories.name as jenis_barang',
                'items.tanggal_pembelian',
                'pengingat_services.date_received as tanggal_service_terakhir',
                'pengingat_services.jadwal_service'
            )
            ->orderBy('jadwal_service', 'asc')
            ->get();
        
         // 2. Ambil data rekomendasi dari JSON
        $rekomendasiPath = public_path('rekomendasi_service.json');
        $rekomendasi = file_exists($rekomendasiPath)
            ? json_decode(file_get_contents($rekomendasiPath), true)
            : [];

        // 3. Gabungkan rekomendasi berdasarkan nama_barang
        foreach ($data as &$row) {
            $row->rekomendasi = 'Tidak Ada';

            foreach ($rekomendasi as $rek) {
                if ($rek['nama_barang'] === $row->nama_barang) {
                    $row->rekomendasi = $rek['rekomendasi'];
                }
            }
        }

        return view('service.regresi_pengingat_service', compact('data'));
    }
    
}
