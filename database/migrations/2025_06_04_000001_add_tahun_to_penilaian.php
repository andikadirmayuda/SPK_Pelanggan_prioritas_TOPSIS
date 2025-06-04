<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->year('tahun')->after('pelanggan_id');
        });
    }

    public function down()
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
