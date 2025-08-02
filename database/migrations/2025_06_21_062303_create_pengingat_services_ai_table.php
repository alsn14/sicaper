<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengingatServicesAiTable extends Migration
{
    public function up()
    {
        Schema::create('pengingat_services_ai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->date('predicted_service_at');
            $table->text('rekomendasi');
            $table->string('status')->default('belum_diservice');
            $table->timestamps();

            // Jika ada relasi ke tabel barang
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengingat_services_ai');
    }
}
