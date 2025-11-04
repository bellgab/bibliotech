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
            $table->string('title');
            $table->string('isbn')->unique();
            $table->integer('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->integer('pages')->nullable();
            $table->string('language')->nullable();
            $table->string('publisher')->nullable();
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('available_copies')->default(1);
            $table->integer('total_copies')->default(1);
            $table->timestamps();

            $table->index(['title']);
            $table->index(['isbn']);
            $table->index(['author_id']);
            $table->index(['category_id']);
            $table->index(['available_copies']);
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
