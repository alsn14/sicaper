<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_in', function (Blueprint $table) {
            $table->string('jenis_pekerjaan');
            $table->string('image')->nullable();
            $table->string('teknisi');
            $table->bigInteger('biaya');
            $table->unsignedBigInteger('supplier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_in', function (Blueprint $table) {
            $table->dropColumn('jenis_pekerjaan');
            $table->dropColumn('image');
            $table->dropColumn('teknisi');
            $table->dropColumn('biaya');
        });
    }
};
