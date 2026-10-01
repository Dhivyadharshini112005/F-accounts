<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {

            $table->string('fees_upi_id')
                ->nullable()
                ->after('fees_payment_mode');

            $table->string('gst_upi_id')
                ->nullable()
                ->after('gst_payment_mode');

        });
    }

    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {

            $table->dropColumn([
                'fees_upi_id',
                'gst_upi_id',
            ]);

        });
    }
};