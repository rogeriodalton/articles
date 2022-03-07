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
            $table->float('min_value')->default(0);
            $table->float('max_value')->default(-1); //indefinido
            $table->boolean('active')->default(true); //indefinido
            $table->timestamps();
        });

        DB::table('discount_orders')->insert([
            [
                'name' => 1,
                'min_value' => 500,
                'max_value' => -1, //sem limite máximo
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
