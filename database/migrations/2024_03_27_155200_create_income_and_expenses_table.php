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
        Schema::create('income_and_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('note');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->date('date');
            $table->integer('amount');
            $table->unsignedBigInteger('payment_mode_id');
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes');
            $table->string('ref_no')->nullable();
            $table->string('type');
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('our_bank_detail_id')->nullable();
            $table->foreign('our_bank_detail_id')->references('id')->on('our_bank_details');
            $table->string('financier_status')->default('Pending');
            $table->string('operation_head_status')->default('Pending');
            $table->string('superviser_status')->default('Pending');
            $table->string('banker_status')->default('Pending');
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_and_expenses');
    }
};
