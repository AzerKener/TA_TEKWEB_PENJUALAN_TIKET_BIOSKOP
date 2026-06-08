<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Studio 1, IMAX Hall, dll
            $table->enum('type', ['regular', 'imax', '4dx', 'vip', 'premiere'])->default('regular');
            $table->unsignedSmallInteger('total_rows');
            $table->string('columns_layout'); // e.g. "1-10" atau "A:10,B:10,C:8"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
