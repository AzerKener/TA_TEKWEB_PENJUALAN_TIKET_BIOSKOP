<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('studio_id')->constrained()->onDelete('cascade');
            $table->date('show_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_regular', 10, 2)->default(50000);
            $table->decimal('price_vip', 10, 2)->default(100000);
            $table->decimal('price_couple', 10, 2)->default(150000);
            $table->enum('language_type', ['dubbed', 'subtitled', 'original'])->default('subtitled');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['show_date', 'studio_id']);
            $table->index(['movie_id', 'show_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
