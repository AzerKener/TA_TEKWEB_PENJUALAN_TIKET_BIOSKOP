<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_token', 64); // untuk identifikasi browser guest
            $table->timestamp('locked_at');
            $table->timestamp('expires_at'); // locked_at + 10 menit
            $table->timestamps();

            $table->unique(['schedule_id', 'seat_id']); // hanya 1 lock per kursi per jadwal
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_locks');
    }
};
