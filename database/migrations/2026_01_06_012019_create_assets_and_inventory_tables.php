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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Untuk QR Code
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('specification')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku_code')->unique();
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock_alert')->default(5);
            $table->string('unit');
            $table->decimal('price_per_unit', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_and_inventory_tables');
    }
};
