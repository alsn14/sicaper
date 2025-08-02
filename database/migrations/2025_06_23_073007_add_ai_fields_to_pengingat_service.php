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
        Schema::table('pengingat_service', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->date('jadwal_service')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengingat_service', function (Blueprint $table) {
            //
        });
    }
};
