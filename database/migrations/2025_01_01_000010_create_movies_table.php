<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis');
            $table->json('genre');
            $table->unsignedSmallInteger('duration'); // dalam menit
            $table->enum('rating', ['SU', 'G', 'PG', 'PG-13', 'R', 'D17'])->default('SU');
            $table->string('director');
            $table->text('cast')->nullable();
            $table->string('poster_image')->nullable();
            $table->string('trailer_url')->nullable();
            $table->enum('language', ['Indonesia', 'Inggris', 'Korea', 'Jepang', 'Mandarin'])->default('Inggris');
            $table->boolean('has_subtitle')->default(true);
            $table->enum('status', ['now_playing', 'coming_soon', 'ended'])->default('coming_soon');
            $table->date('release_date');
            $table->date('end_date')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->string('production_company')->nullable();
            $table->string('distributor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('release_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
