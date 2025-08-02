<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->integer('biaya_service')->nullable()->after('tanggal_service');
            $table->string('teknisi')->nullable()->after('biaya_service');
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->date('tanggal_service');
            $table->integer('biaya_service')->nullable();
            $table->string('teknisi')->nullable(); // kalau kamu mau simpan
            $table->text('keterangan')->nullable(); // dari jenis_pekerjaan
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
