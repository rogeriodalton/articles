<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name');
            $table->string('fname')->index();
            $table->string('email');
            $table->timestamps();
        });

        $f = [
            'José da Silva' => phonetics('José da Silva'),
            'Emanuel Gomes' => phonetics('Emanuel Gomes'),
            'Doroteia Vernek' => phonetics('Doroteia Vernek'),
        ];

        DB::table('clients')->insert([
            [
                'name' => 'José da Silva',
                'fname' => $f['José da Silva'],
                'email' => 'jose@jose.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Emanuel Gomes',
                'fname' => $f['Emanuel Gomes'],
                'email' => 'emmanuel@emmanuel.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Doroteia Vernek',
                'fname' => $f['Doroteia Vernek'],
                'email' => 'doroteia@doroteia.com',
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
        Schema::dropIfExists('clients');
    }
}
