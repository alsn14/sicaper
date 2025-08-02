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
        });
    }

    public function down()
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn(['biaya_service', 'teknisi']);
        });
    }

};
