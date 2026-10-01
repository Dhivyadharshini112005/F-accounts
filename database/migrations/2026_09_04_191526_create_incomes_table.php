<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');

            $table->decimal('amount', 12, 2);

            $table->date('income_date');

            $table->string('description')
                ->nullable();

            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('incomes');
    }
};