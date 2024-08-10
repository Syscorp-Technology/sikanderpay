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
        Schema::create('benificiaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bank_detail_id')->unsigned();
            $table->bigInteger('our_bank_details_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('bank_details_id')->references('id')->on('bank_details');
            $table->foreign('our_bank_details_id')->references('id')->on('our_bank_details');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benificiaries');
    }
};
