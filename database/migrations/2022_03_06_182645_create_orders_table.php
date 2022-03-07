<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients'); //cliente
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users'); //vendedor
            $table->string('code', 10);
            $table->string('date', 10);
            $table->float('amount_liquid');
            $table->float('amount_discount');
            $table->float('amount_add');
            $table->float('amount_gross');
            $table->timestamps();
        });

        DB::table('orders')->insert([
            [
                'client_id' => 1,
                'user_id' => 2, //vendedor
                'code' => 'XYZ0001',
                'date' => '2022-03-06',
                'amount_liquid' => 78.77,
                'amount_discount' => 0,
                'amount_add' => 0,
                'amount_gross' => 78.77,
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
        Schema::dropIfExists('orders');
    }
}
