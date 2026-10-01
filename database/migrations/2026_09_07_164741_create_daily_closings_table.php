<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();

            $table->date('closing_date')->unique();

            $table->decimal('opening_balance', 15, 2)->default(0);

            $table->decimal('total_income', 15, 2)->default(0);

            $table->decimal('total_expense', 15, 2)->default(0);

            $table->decimal('closing_balance', 15, 2)->default(0);

            $table->string('closed_by')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_closings');
    }
};