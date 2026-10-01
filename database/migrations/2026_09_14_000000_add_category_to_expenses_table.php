<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('expenses', 'category')) {
            Schema::table('expenses', function (Blueprint $table) {
                // Do not use "after('expense_name')" because the existing
                // expenses table does not have an expense_name column.
                $table->string('category', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'category')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
