<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->foreignId('sparepart_category_id')->nullable()->after('id')->constrained('sparepart_categories')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sparepart_category_id');
        });
    }
};
