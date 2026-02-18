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
        Schema::table('assets', function (Blueprint $table) {
            $table->json('problem_history')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('problem_history');
        });
    }
};
