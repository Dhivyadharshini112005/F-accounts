<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('expenses', 'upi_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('upi_id')->nullable()->after('payment_mode');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('expenses', 'upi_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('upi_id');
            });
        }
    }
};
