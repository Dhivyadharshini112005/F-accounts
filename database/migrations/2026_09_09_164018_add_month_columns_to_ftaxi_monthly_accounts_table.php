<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ftaxi_monthly_accounts', function (Blueprint $table) {

            $table->string('month')->unique()->after('id');

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
        });
    }

    public function down()
    {
        Schema::table('ftaxi_monthly_accounts', function (Blueprint $table) {
            $table->dropUnique(['month']);
            $table->dropColumn([
                'month',
                'driver_payout',
                'driver_amt',
                'company_amt',
                'commission',
                'gst',
                'business_commission',
                'pending_amt',
                'amount_paid',
                'advance_amount',
                'toll_fee',
                'service_charge',
            ]);
        });
    }
};