<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->integer('units')->default(1);
            $table->float('unit_value')->default(0);
            $table->float('amount_liquid')->nullable();
            $table->float('amount_discount')->nullable();
            $table->float('amount_add')->nullable();
            $table->float('amount_gross')->nullable();
            $table->timestamps();
        });

        DB::table('order_items')->insert([
            [
                'order_id' => 1,
                'article_id' => 1,
                'units' => 6,
                'unit_value' => 10.11,
                'amount_liquid' => 30.33,
                'amount_discount' => 0,
                'amount_add' => 0,
                'amount_gross' => 30.33,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'order_id' => 1,
                'article_id' => 2,
                'units' => 4,
                'unit_value' => 12.11,
                'amount_liquid' => 48.44,
                'amount_discount' => 0,
                'amount_add' => 0,
                'amount_gross' => 48.44,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
