<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->onDelete('cascade');
            $table->string('row_label', 2); // A, B, C, ... Z, AA
            $table->unsignedSmallInteger('seat_number'); // 1, 2, 3...
            $table->string('seat_code', 10); // A1, B12, dll
            $table->enum('type', ['regular', 'couple', 'vip', 'disabled'])->default('regular');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['studio_id', 'seat_code']);
            $table->index(['studio_id', 'row_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
