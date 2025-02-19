<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estimation_id')->constrained()->onDelete('cascade');
        $table->decimal('final_cost', 10, 2);
        $table->string('payment_terms');
        $table->date('due_date');
        $table->text('additional_notes')->nullable();
        $table->foreignId('biller_id')->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
