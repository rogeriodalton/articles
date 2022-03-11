<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('code')->index();
            $table->string('name');
            $table->string('fname')->index();
            $table->float('price');
            $table->string('quantity');
            $table->timestamps();
        });

        $f = [
            'primeiro artigo' => phonetics('primeiro artigo'),
            'segundo artigo' => phonetics('segundo artigo'),
            'terceiro artigo' => phonetics('terceiro artigo'),
            'quarto artigo' => phonetics('quarto artigo'),
            'quinto artigo' => phonetics('quinto artigo'),
        ];

        DB::table('articles')->insert([
            [
                'code' => 'aaa001',
                'name' => 'primeiro artigo',
                'fname' => $f['primeiro artigo'],
                'price' => 10.11,
                'quantity' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'aaa002',
                'name' => 'segundo artigo',
                'fname' => $f['segundo artigo'],
                'price' => 12.11,
                'quantity' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'aaa003',
                'name' => 'terceiro artigo',
                'fname' => $f['terceiro artigo'],
                'price' => 13.03,
                'quantity' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'aaa004',
                'name' => 'quarto artigo',
                'fname' => $f['quarto artigo'],
                'price' => 14.04,
                'quantity' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'aaa005',
                'name' => 'quinto artigo',
                'fname' => $f['quinto artigo'],
                'price' => 15.12,
                'quantity' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
