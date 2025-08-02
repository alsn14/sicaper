<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportServiceRecords()
    {
        $records = DB::table('service_records')->get();

        $csvHeader = ['item_id', 'tanggal_service', 'biaya_service', 'teknisi', 'keterangan'];
        $filename = 'service_records_export.csv';

        $callback = function () use ($records, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->item_id,
                    $row->tanggal_service,
                    $row->biaya_service,
                    $row->teknisi,
                    $row->keterangan
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
