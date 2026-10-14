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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique()->nullable();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('publish_year')->nullable();
            $table->string('edition')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('language')->default('Indonesian');
            $table->integer('page_count')->nullable();
            $table->string('dimensions')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rack_location')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['title', 'author']);
            $table->index('isbn');
            if (config('database.default') !== 'sqlite') {
                $table->fullText(['title', 'author', 'publisher']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};