<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Room;

class ItemController extends Controller
{
    public function index():View
    {
        $jenisbarang = Category::all();
        $satuan = Unit::all();
        $merk = Brand::all();
        $room= Room::all();
        return view('admin.master.barang.index',compact('jenisbarang','satuan','merk','room'));
    }
    public function list(Request $request): JsonResponse
    {
        $items = Item::with('category','unit','brand','room.pic')->latest()->get();
        if($request -> ajax()){
            return DataTables::of($items)
            ->addColumn('img',function($data){
                if(empty($data->image)){
                    return "<img src='".asset('default.png')."' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                }
                return "<img src='".asset('storage/barang/'.$data->image)."' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
            })
            -> addColumn('category_name',function($data){
                return $data->category->name;
            })
            -> addColumn('unit_name',function($data){
                return $data->unit->name;
            })
            -> addColumn('brand_name',function($data){
                return $data -> brand -> name;
            })
            -> addColumn('room_name',function($data){
                return $data -> room?-> name??'';
            })
            ->addColumn('penanggungjawab', function($data){
                return $data->room->pic->name ?? 'Undefined';
            })
            
            -> addColumn('tindakan',function($data){
                    $button = "<button class='ubah btn btn-success m-1' id='".$data->id."'><i class='fas fa-pen m-1'></i>".__("edit")."</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='".$data->id."'><i class='fas fa-trash m-1'></i>".__("delete")."</button>";
                    return $button;
            })
            ->rawColumns(['img','tindakan'])
            -> make(true);

        }
    }

    public function save(Request $request): JsonResponse
    {
        $request->validate([
        'tanggal_pembelian' => 'required|date'
    ]);
        $data = [
            'name'=>$request->name,
            'code'=>$request->code,
            'price'=>$request->price,
            'category_id'=>$request->category_id,
            'brand_id'=>$request->brand_id,
            'unit_id'=>$request->unit_id,
            'room_id'=>$request->room_id,
            'penanggungjawab'=>$request->penanggungjawab,
            'tanggal_pembelian'=>$request->tanggal_pembelian,
        ];
        if ($request->file('image') != null) {
            $image = $request->file('image');
            $image->storeAs('public/barang/', $image->hashName());
            $img = $image->hashName();
            $data['image']=$img;
        }
        Item::create($data);
        return response() -> json([
            "message"=>__("saved successfully")
        ]) -> setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request -> id;
        $data = Item::with('category','unit','brand','room')->find($id);
        $data ['category_name'] = $data -> category -> name;
        $data ['unit_name'] = $data -> unit -> name;
        $data ['room_name'] = $data -> room?-> name??'';
        return response()->json(
            ["data"=>$data]
        )->setStatusCode(200);
    }

    public function detailByCode(Request $request): JsonResponse
    {
        $code = $request->code;
        $data = Item::with('category','unit','brand','room')->where("code",$code)->first();
        $data ['category_name'] = $data -> category -> name;
        $data ['unit_name'] = $data -> unit -> name;
        $data ['room_name'] = $data -> room?-> name??'';
        return response()->json(
            ["data"=>$data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
         $request->validate([
        'tanggal_pembelian' => 'required|date'
    ]);

        $id = $request -> id;
        $item = Item::find($id);
        $data = [
            'name'=>$request->name,
            'code'=>$request->code,
            'price'=>$request->price,
            'category_id'=>$request->category_id,
            'brand_id'=>$request->brand_id,
            'unit_id'=>$request->unit_id,
            'room_id'=>$request->room_id,
            'penanggungjawab'=>$request->penanggungjawab,
            'tanggal_pembelian'=>$request->tanggal_pembelian,
        ];
        if ($request->file('image') != null) {
            Storage::delete('public/barang/'.$item->image);
            $image = $request->file('image');
            $image->storeAs('public/barang/', $image->hashName());
            $img = $image->hashName();
            $data['image']=$img;
        }
        $item -> fill($data);
        $item -> save();
        return response() -> json([
            "message"=>__("data changed successfully")
        ]) -> setStatusCode(200);

    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request -> id;
        $item = Item::find($id);
        Storage::delete('public/barang/'.$item->image);
        $status = $item -> delete();
        if(!$status){
            return response()->json(
                ["message"=>__("data failed to delete")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message"=>__("data deleted successfully")
        ]) -> setStatusCode(200);
    }
}
