<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengingatServicesTable extends Migration
{
    public function up()
    {
        Schema::create('service.pengingat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable(); // Relasi ke item
            $table->date('date_received')->nullable();         // Tanggal terakhir service
            $table->string('status')->default('belum');         // Status service
            $table->date('jadwal_service')->nullable();         // Jadwal service berikutnya
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service.pengingat');
    }
}
