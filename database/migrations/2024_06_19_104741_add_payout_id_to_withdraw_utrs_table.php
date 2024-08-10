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
        Schema::table('withdraw_utrs', function (Blueprint $table) {
            $table->string('payout_id')->nullable()->after('utr');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraw_utrs', function (Blueprint $table) {
            $table->string('payout_id')->nullable()->after('utr');

        });
    }
};
