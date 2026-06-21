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
        Schema::table('listings', function (Blueprint $table) {
            $table->unsignedTinyInteger('district_id')->nullable()->after('address');
            $table->string('district_name')->nullable()->after('district_id');
            $table->string('poster_type')->nullable()->after('district_name'); // 'owner' or 'agent'
            $table->timestamp('listed_at')->nullable()->after('poster_type');
            $table->index('district_id');
            $table->index('listed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'district_name', 'poster_type', 'listed_at']);
        });
    }
};
