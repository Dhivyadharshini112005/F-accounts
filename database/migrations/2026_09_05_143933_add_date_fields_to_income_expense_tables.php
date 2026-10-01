<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {

            $table->date('income_date')
                  ->after('amount')
                  ->nullable();

        });


        Schema::table('expenses', function (Blueprint $table) {

            $table->date('expense_date')
                  ->after('amount')
                  ->nullable();

        });
    }


    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('income_date');
        });


        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('expense_date');
        });
    }

};