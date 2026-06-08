<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('restrict');
            $table->foreignId('schedule_id')->constrained()->onDelete('restrict');
            $table->string('ticket_code', 20)->unique(); // TKT-XXXXXXXX
            $table->enum('seat_type', ['regular', 'couple', 'vip', 'disabled'])->default('regular');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'used', 'cancelled'])->default('active');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['seat_id', 'schedule_id']); // satu kursi satu kali per jadwal
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
