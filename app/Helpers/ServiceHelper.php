<?php

namespace App\Helpers;

use Carbon\Carbon;

class ServiceHelper
{
    public static function predictNextServiceDate($lastServiceDate, $minMonths, $maxMonths)
    {
        $date = new Carbon($lastServiceDate);
        $intervalMonths = rand($minMonths, $maxMonths); // Menggunakan interval waktu acak di antara batas bawah dan batas atas
        $date->modify("+{$intervalMonths} months");
        return $date->format('Y-m-d');
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