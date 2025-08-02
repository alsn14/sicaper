<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;

class ServiceRecordController extends Controller
{
    // Tampilkan daftar service semua barang
    public function index()
    {
        $records = ServiceRecord::with('item')->latest()->get();
        return view('service.records.index', compact('records'));
    }

    // Form tambah catatan service untuk barang tertentu
    public function create($itemId)
    {
        $item = Item::findOrFail($itemId);
        return view('service.records.create', compact('item'));
    }

    // Simpan catatan service
    public function store(Request $request, $itemId)
    {
        $request->validate([
            'tanggal_service' => 'required|date',
            'keterangan' => 'nullable|string',
            'biaya' => 'nullable|numeric',
        ]);

        ServiceRecord::create([
            'item_id' => $itemId,
            'tanggal_service' => $request->tanggal_service,
            'keterangan' => $request->keterangan,
            'biaya' => $request->biaya,
        ]);

        return redirect()->route('service-records.index')->with('success', 'Catatan service berhasil ditambahkan.');
    }

    public static function updateTanggalServiceTerakhir($item_id)
    {
        $latest = \App\Models\ServiceRecord::where('item_id', $item_id)
            ->orderByDesc('tanggal_service')
            ->first();

        $item = \App\Models\Item::find($item_id);
        $item->tanggal_service_terakhir = $latest ? $latest->tanggal_service : null;
        $item->save();
    }

    

}

