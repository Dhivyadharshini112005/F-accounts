<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('fees_payment_mode', 20)
                ->default('cash')
                ->after('fees_amount');

            $table->string('gst_payment_mode', 20)
                ->default('cash')
                ->after('gst_amount');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('payment_mode', 20)
                ->default('cash')
                ->after('amount');
        });
    }

    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn([
                'fees_payment_mode',
                'gst_payment_mode',
            ]);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('payment_mode');
        });
    }
};