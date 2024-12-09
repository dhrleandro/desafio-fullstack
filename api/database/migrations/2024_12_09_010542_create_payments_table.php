<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->decimal('plan_price', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('amount_charged', 10, 2);
            $table->decimal('credit_remaining', 10, 2);
            $table->timestampTz('due_date')->nullable();
            $table->string('status');
            $table->timestampsTz();

            $table->foreign('contract_id')->references('id')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
