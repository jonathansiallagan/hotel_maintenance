<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->json('rca_history')->nullable(); // Menambahkan memori ke Kategori
        });
    }

    public function down()
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('rca_history');
        });
    }
};
