<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_closings', function (Blueprint $table) {

            if (!Schema::hasColumn('daily_closings', 'closing_date')) {
                $table->date('closing_date')
                    ->nullable()
                    ->after('id');
            }

            if (!Schema::hasColumn('daily_closings', 'opening_balance')) {
                $table->decimal('opening_balance', 15, 2)
                    ->default(0)
                    ->after('closing_date');
            }

            if (!Schema::hasColumn('daily_closings', 'total_income')) {
                $table->decimal('total_income', 15, 2)
                    ->default(0)
                    ->after('opening_balance');
            }

            if (!Schema::hasColumn('daily_closings', 'total_expense')) {
                $table->decimal('total_expense', 15, 2)
                    ->default(0)
                    ->after('total_income');
            }

            if (!Schema::hasColumn('daily_closings', 'closing_balance')) {
                $table->decimal('closing_balance', 15, 2)
                    ->default(0)
                    ->after('total_expense');
            }

            if (!Schema::hasColumn('daily_closings', 'closed_by')) {
                $table->string('closed_by')
                    ->nullable()
                    ->after('closing_balance');
            }

            if (!Schema::hasColumn('daily_closings', 'closed_at')) {
                $table->timestamp('closed_at')
                    ->nullable()
                    ->after('closed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_closings', function (Blueprint $table) {

            if (Schema::hasColumn('daily_closings', 'closed_at')) {
                $table->dropColumn('closed_at');
            }

            if (Schema::hasColumn('daily_closings', 'closed_by')) {
                $table->dropColumn('closed_by');
            }

            if (Schema::hasColumn('daily_closings', 'closing_balance')) {
                $table->dropColumn('closing_balance');
            }

            if (Schema::hasColumn('daily_closings', 'total_expense')) {
                $table->dropColumn('total_expense');
            }

            if (Schema::hasColumn('daily_closings', 'total_income')) {
                $table->dropColumn('total_income');
            }

            if (Schema::hasColumn('daily_closings', 'opening_balance')) {
                $table->dropColumn('opening_balance');
            }

            if (Schema::hasColumn('daily_closings', 'closing_date')) {
                $table->dropColumn('closing_date');
            }
        });
    }
};