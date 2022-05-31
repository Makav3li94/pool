<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->string('token1_symbol');
            $table->string('token2_symbol');
            $table->string('eth_res');
            $table->string('tet_res');
            $table->string('eth_amount');
            $table->string('tet_amount');
            $table->string('eth_buy_price');
            $table->string('eth_sell_price');
            $table->string('eth_global_price');
            $table->string('eth_fee');
            $table->string('tet_fee');
            $table->tinyInteger('input_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pools');
    }
};
