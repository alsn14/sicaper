<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function index():View
    {
$penanggungjawab=Pic::all();
        return view('admin.master.room.index',compact('penanggungjawab'));
    }
    public function list(Request $request): JsonResponse
    {
        $room = Room::latest()->with('pic')->get();
        if($request -> ajax()){
            return DataTables::of($room)
            -> addColumn('name',function($data){
                return $data -> name;
            })
            -> addColumn('pic_id',function($data){
                return $data -> pic->name;
            })
            ->addColumn('penanggungjawab', function ($data) {
                return $data->pic->name ?? '-';
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
        $data = [
            'name'=>$request->name,
            'pic_id'=>$request->pic_id,
        ];
        Room::create($data);
        return response() -> json([
            "message"=>__("saved successfully")
        ]) -> setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request -> id;
        $data = Room::find($id);
        return response()->json(
            ["data"=>$data]
        )->setStatusCode(200);
    }
    public function update(Request $request): JsonResponse
    {
        $id = $request -> id;
        $room = Room::find($id);
        $data = [
            'name'=>$request->name,
            'pic_id'=>$request->pic_id,
        ];
        $room -> fill($data);
        $room -> save();
        return response() -> json([
            "message"=>__("data changed successfully")
        ]) -> setStatusCode(200);

    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request -> id;
        $room = Room::find($id);
        $status = $room -> delete();
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
