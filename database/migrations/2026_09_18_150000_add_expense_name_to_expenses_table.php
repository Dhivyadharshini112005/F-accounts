<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('expenses', 'expense_name')) {

            Schema::table('expenses', function (Blueprint $table) {
                $table->string('expense_name')->nullable()->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('expenses', 'expense_name')) {

            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('expense_name');
            });
        }
    }
};