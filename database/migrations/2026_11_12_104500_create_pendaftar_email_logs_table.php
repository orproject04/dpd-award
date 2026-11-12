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
        Schema::create('pendaftar_email_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftar_id')->index()->constrained('pendaftar')->cascadeOnDelete();
            $table->string('email_type', 50)->default('sertifikat')->index();
            $table->string('stage_key', 50)->nullable()->index();
            $table->string('stage_label', 150)->nullable();
            $table->string('email', 150);
            $table->string('status', 20)->index(); // 'sent', 'failed'
            $table->text('error_message')->nullable();
            $table->foreignUlid('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['pendaftar_id', 'email_type', 'stage_key', 'status'], 'idx_pendaftar_email_logs_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftar_email_logs');
    }
};

