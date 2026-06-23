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
        Schema::table('saved_listings', function (Blueprint $table) {
            $table->date('saved_date')->nullable()->after('listing_id');
        });
        // Back-fill existing rows with today's date
        \DB::table('saved_listings')->whereNull('saved_date')->update(['saved_date' => now()->toDateString()]);
    }

    public function down(): void
    {
        Schema::table('saved_listings', function (Blueprint $table) {
            $table->dropColumn('saved_date');
        });
    }
};
