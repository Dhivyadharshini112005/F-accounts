<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('incomes', 'fees_payment_mode')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->string('fees_payment_mode', 20)
                    ->default('cash')
                    ->after('fees_amount');
            });
        }

        if (!Schema::hasColumn('incomes', 'gst_payment_mode')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->string('gst_payment_mode', 20)
                    ->default('cash')
                    ->after('gst_amount');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('incomes', 'fees_payment_mode')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->dropColumn('fees_payment_mode');
            });
        }

        if (Schema::hasColumn('incomes', 'gst_payment_mode')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->dropColumn('gst_payment_mode');
            });
        }
    }
};