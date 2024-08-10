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
        Schema::table('our_bank_details', function (Blueprint $table) {
            $table->integer('limit')->default(0)->after('amount');
            // $table->integer('count')->default(0)->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('our_bank_details', function (Blueprint $table) {
            //
        });
    }
};
