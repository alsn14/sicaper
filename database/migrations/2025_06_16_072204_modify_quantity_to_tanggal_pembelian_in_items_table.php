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
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('quantity'); // hapus kolom lama
        $table->date('tanggal_pembelian')->nullable(); // tambahkan kolom baru
    });
}

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(0); // kembalikan kalau rollback
            $table->dropColumn('tanggal_pembelian'); // hapus kalau rollback
        });
    }
};
