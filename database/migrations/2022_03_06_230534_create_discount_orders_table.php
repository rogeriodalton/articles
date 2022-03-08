<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateDiscountOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_orders', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name')->nullable();
            $table->float('value_min')->default(0);
            $table->float('value_max')->default(-1); //indefinido
            $table->float('discount_percent')->default(0);
            $table->boolean('active')->default(true); //indefinido
            $table->timestamps();
        });

        DB::table('discount_orders')->insert([
            [
                'name' => 'Desconto teste',
                'value_min' => 500,
                'value_max' => -1, //sem limite máximo
                'discount_percent' => 15,
                'active' => true,
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
        Schema::dropIfExists('discount_orders');
    }
}
