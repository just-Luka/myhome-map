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
            $table->string('link_myhome')->nullable()->after('note');
            $table->string('link_ss')->nullable()->after('link_myhome');
        });
    }

    public function down(): void
    {
        Schema::table('saved_listings', function (Blueprint $table) {
            $table->dropColumn(['link_myhome', 'link_ss']);
        });
    }
};
