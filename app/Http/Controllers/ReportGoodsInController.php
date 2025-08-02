<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\GoodsIn;
use App\Models\Item;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ReportGoodsInController extends Controller
{
    public function index(): View
    {
        $items=Item::all();

        return view('admin.master.laporan.masuk',compact('items'));
    }

    public function list(Request $request):JsonResponse
    {
        if($request->ajax()){
            if( empty($request->start_date) && empty($request->end_date)){
                $goodsins = GoodsIn::with('item','user','supplier');
            }else{
                $goodsins = GoodsIn::with('item','user','supplier');
                $goodsins -> whereBetween('date_received',[$request->start_date,$request->end_date]);
            }
            if ($request->item_id) {
                $goodsins = GoodsIn::whereHas('item', fn($query) => $query->where('items.id', $request->item_id));
            }
            $goodsins -> latest() -> get();
            return DataTables::of($goodsins)
            ->addColumn('quantity',function($data){
                $item = Item::with("unit")->find($data -> item -> id);
                return $data -> quantity ."/".$item -> unit -> name;
            })
            ->addColumn("date_received",function($data){
                return Carbon::parse($data->date_received)->format('d F Y');
            })
            ->addColumn("kode_barang",function($data){
                return $data -> item -> code;
            })
            ->addColumn("supplier_name",function($data){
                return $data -> supplier?-> name;
            })
            ->addColumn("item_name",function($data){
                return $data -> item?-> name;
            })
            ->editColumn('image',function($data){
                return asset('storage/service/'.$data->image);
            })
            -> make(true);
        }
    }

}
