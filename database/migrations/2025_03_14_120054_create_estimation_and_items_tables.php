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
        // First, create the estimations table
        Schema::create('estimations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->string('service_advisor');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
        
        // Then, create the estimation_items table
        Schema::create('estimation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimation_id');
            $table->unsignedBigInteger('service_request_id');
            $table->string('part_number')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
            
            // Add foreign key constraints
            $table->foreign('estimation_id')->references('id')->on('estimations')->onDelete('cascade');
            $table->foreign('service_request_id')->references('id')->on('service_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraint issues
        Schema::dropIfExists('estimation_items');
        Schema::dropIfExists('estimations');
    }
};