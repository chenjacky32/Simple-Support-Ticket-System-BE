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
        Schema::create('ticket_status_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignUlid('changed_by')->nullable()->constrained('users');
            $table->enum('previous_status', ['OPENED', 'INPROGRESS', 'RESOLVED']);
            $table->enum('current_status', ['OPENED', 'INPROGRESS', 'RESOLVED']);
            $table->text('result')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_logs');
    }
};
