<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['food', 'drink', 'combo', 'snack'])->default('food');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('stock')->default(100);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_items');
    }
};
