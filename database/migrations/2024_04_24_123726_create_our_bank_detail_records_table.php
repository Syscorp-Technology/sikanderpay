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
        Schema::create('our_bank_detail_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('our_bank_detail_id')->unsigned();
            $table->foreign('our_bank_detail_id')->references('id')->on('our_bank_details');
            $table->string('type');
            $table->string('operation');
            $table->string('bank_amount');
            $table->string('req_amount');
            $table->string('updated_amount');
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_bank_detail_records');
    }
};
