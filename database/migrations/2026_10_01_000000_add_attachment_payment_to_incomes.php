<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'attachment_amount')) {
                $table->decimal('attachment_amount', 12, 2)->default(0)->after('fees_amount');
            }
            if (!Schema::hasColumn('incomes', 'attachment_payment_mode')) {
                $table->string('attachment_payment_mode', 20)->default('cash')->after('fees_payment_mode');
            }
            if (!Schema::hasColumn('incomes', 'attachment_upi_id')) {
                $table->string('attachment_upi_id')->nullable()->after('fees_upi_id');
            }
        });
    }

    public function down()
    {
        $columns = [];
        foreach (['attachment_amount', 'attachment_payment_mode', 'attachment_upi_id'] as $column) {
            if (Schema::hasColumn('incomes', $column)) {
                $columns[] = $column;
            }
        }
        if ($columns) {
            Schema::table('incomes', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
