<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengingatService;
use Carbon\Carbon;
use App\Models\GoodsIn;
use App\Models\Category;

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

        $scriptPath = public_path('ai/regresi.py');
        $command = "python3 $scriptPath $jenis $tanggalPembelian $tanggalServiceTerakhir";

        $output = shell_exec($command);

        return response()->json([
            'hasil_prediksi_hari' => intval(trim($output))
        ]);
    }

}
