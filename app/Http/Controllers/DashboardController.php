<?php

namespace App\Http\Controllers;

use Carbon\Carbon; // <- perbaiki "carbon" jadi "Carbon"
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Role;
use App\Models\User;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\Customer;
use App\Models\PengingatService;
use App\Models\Service; 
use App\Models\ServicePrediction;
use App\Models\ServiceRecord;

class DashboardController extends Controller
{
    public function index(): View
    {
        $product_count = Item::count();
        $category_count = Category::count();
        $unit_count = Unit::count();
        $brand_count = Brand::count();
        $goodsin = GoodsIn::count();
        $goodsout = GoodsOut::count();
        $customer = Customer::count();
        $supplier = Supplier::count();
        $staffCount = User::where('role_id',2)->count();

        // ===========================================
        // LOGIKA TAMBAHAN: generate data pengingat otomatis
        // ===========================================
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
        

        // Setelah itu ambil data pengingat
        $pengingatService = PengingatService::with('item.category')->orderBy('date_received', 'asc')
            ->take(5)
            ->get();


        // Mengambil semua data dari tabel ServicePrediction
        $servicePredictions = ServiceRecord::all();

        // Mengambil data pengingat service yang sudah ada
        return view('admin.dashboard', compact(
            'product_count',
            'category_count',
            'unit_count',
            'brand_count',
            'goodsin',
            'goodsout',
            'customer',
            'supplier',
            'staffCount',
            'pengingatService',
            'servicePredictions'
        ));
    }
}
