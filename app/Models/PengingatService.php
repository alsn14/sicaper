<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengingatService extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'date_received',
        'status',
        'jadwal_service',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getRekomendasiAttribute()
    {
        $jenis = $this->item->category->name ?? null;

        if ($jenis == 'Komputer') {
            return 'Cleaning dan Ganti Pasta Thermal';
        } elseif ($jenis == 'Perabotan Kayu') {
            return 'Pengecekan dan Perawatan Finishing';
        } elseif ($jenis == 'Printer') {
            return 'Cleaning Head Printer';
        } else {
            return 'Pengecekan rutin 6 bulan';
        }
    }
}
