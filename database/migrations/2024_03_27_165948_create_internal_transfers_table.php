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
        Schema::create('internal_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_from');
            $table->unsignedBigInteger('bank_to');
            $table->integer('amount');
            $table->string('superviser_status')->default('Pending');
            $table->string('banker_status')->default('Pending');
            $table->string('utr');
            $table->string('attachment')->nullable();
            $table->text('remark');
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
        Schema::dropIfExists('internal_transfers');
    }
};
