<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->decimal('fees_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('gst_percentage', 5, 2)->nullable()->after('fees_amount');
            $table->decimal('gst_amount', 12, 2)->nullable()->after('gst_percentage');
            $table->decimal('total_amount', 12, 2)->nullable()->after('gst_amount');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn([
                'fees_amount',
                'gst_percentage',
                'gst_amount',
                'total_amount',
            ]);
        });
    }
};
