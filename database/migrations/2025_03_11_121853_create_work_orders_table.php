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
    Schema::create('work_orders', function (Blueprint $table) {
        $table->id();
        $table->string('no_polisi');
        $table->integer('kilometer');
        $table->string('no_spk')->unique();
        $table->string('type_kendaraan');
        $table->string('customer_name');
        $table->text('keluhan')->nullable();
        $table->unsignedBigInteger('user_id');
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });

   
    Schema::table('service_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('work_order_id')->after('user_id')->nullable();
        $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('service_requests', function (Blueprint $table) {
        $table->dropForeign(['work_order_id']);
        $table->dropColumn('work_order_id');
    });
    Schema::dropIfExists('work_orders');
}
};
