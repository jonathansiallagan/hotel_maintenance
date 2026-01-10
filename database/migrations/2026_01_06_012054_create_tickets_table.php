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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('photo_evidence_before');
            $table->string('photo_evidence_after')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('status')->default('open')->index();

            // SLA Timestamps
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('sla_deadline')->nullable();

            $table->text('technician_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
