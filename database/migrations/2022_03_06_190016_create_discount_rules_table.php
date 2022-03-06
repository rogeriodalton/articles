<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('articles_id')->unsigned();
            $table->foreign('articles_id')->references('id')->on('articles');
            $table->integer('units_min')->nullable();
            $table->integer('units_max')->nullable();
            $table->float('value_min')->nullable();
            $table->float('value_max')->nullable();
            $table->float('discount_percent')->default(0);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('discount_rules');
    }
}
