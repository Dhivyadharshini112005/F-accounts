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
        Schema::create('ftaxi_monthly_accounts', function (Blueprint $table) {
            $table->id();

            // Month identifier, example: Aug-26
            $table->string('month')->unique();

            // F-Taxi monthly account values
            $table->decimal('driver_payout', 15, 2)->default(0);
            $table->decimal('driver_amt', 15, 2)->default(0);
            $table->decimal('company_amt', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('gst', 15, 2)->default(0);
            $table->decimal('business_commission', 15, 2)->default(0);
            $table->decimal('pending_amt', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('advance_amount', 15, 2)->default(0);
            $table->decimal('toll_fee', 15, 2)->default(0);
            $table->decimal('service_charge', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ftaxi_monthly_accounts');
    }
};