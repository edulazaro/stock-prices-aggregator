<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 9)->unique();
            $table->decimal('price', 17, 4)->nullable();
            $table->bigInteger('volume')->nullable();
            $table->decimal('open', 17, 4)->nullable();
            $table->decimal('high', 17, 4)->nullable();
            $table->decimal('low', 17, 4)->nullable();
            $table->decimal('previous_close', 17, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
