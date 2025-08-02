<?php

namespace App\Http\Controllers;

use App\Models\Pic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PicController extends Controller
{
    public function index():View
    {

        return view('admin.master.pic.index');
    }
    public function list(Request $request): JsonResponse
    {
        $pic = Pic::latest()->get();
        if($request -> ajax()){
            return DataTables::of($pic)
            -> addColumn('nomer',function($data){
                return $data -> nomer;
            })
            -> addColumn('name',function($data){
                return $data -> name;
            })
            -> addColumn('nip',function($data){
                return $data -> nip;
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
            'nomer'=>$request->nomer,
            'name'=>$request->name,
            'nip'=>$request->nip,
        ];
        Pic::create($data);
        return response() -> json([
            "message"=>__("saved successfully")
        ]) -> setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request -> id;
        $data = Pic::find($id);
        return response()->json(
            ["data"=>$data]
        )->setStatusCode(200);
    }
    public function update(Request $request): JsonResponse
    {
        $id = $request -> id;
        $pic = Pic::find($id);
        $data = [
            'nomer'=>$request->nomer,
            'name'=>$request->name,
            'nip'=>$request->nip,
        ];
        $pic -> fill($data);
        $pic -> save();
        return response() -> json([
            "message"=>__("data changed successfully")
        ]) -> setStatusCode(200);

    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request -> id;
        $pic = Pic::find($id);
        $status = $pic -> delete();
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
