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
        Schema::create('approval_work_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->unsignedBigInteger('withdraw_id')->nullable();
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('cascade');
            $table->foreign('withdraw_id')->references('id')->on('withdraws')->onDelete('cascade');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('banker_id')->nullable();
            $table->unsignedBigInteger('cc_id')->nullable();
            $table->timestamp('admin_status_at')->nullable();
            $table->timestamp('banker_status_at')->nullable();
            $table->timestamp('cc_status_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
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
        Schema::dropIfExists('approval_work_timelines');
    }
};
