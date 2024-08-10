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
            $table->string('type')->default('Bank');
            $table->unsignedInteger('category')->nullable();
            // $table->foreign('category')->references('id')->on('gateway_categories');
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
