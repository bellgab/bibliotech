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
        Schema::table('book_borrowings', function (Blueprint $table) {
            if (!Schema::hasColumn('book_borrowings', 'fine_amount')) {
                $table->decimal('fine_amount', 8, 2)->nullable()->after('returned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('book_borrowings', 'fine_amount')) {
                $table->dropColumn('fine_amount');
            }
        });
    }
};
