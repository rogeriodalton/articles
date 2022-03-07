<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateAccessGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_groups', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name');
            $table->string('fname')->index();
            $table->timestamps();
        });

        $f = [
            'Administração' => phonetics('Administração'),
            'Vendas' => phonetics('Vendas'),
            'Contas a pagar' => phonetics('Contas a pagar'),
            'Contas a Receber' => phonetics('Contas a Receber'),
            'Compras' => phonetics('Compras'),
            'Suporte técnico' => phonetics('Suporte técnico'),
        ];

        DB::table('access_groups')->insert([
            [
                'name' => 'Administração',
                'fname' => $f['Administração'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Suporte técnico',
                'fname' => $f['Suporte técnico'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Vendas',
                'fname' => $f['Vendas'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Contas a pagar',
                'fname' => $f['Contas a pagar'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Contas a Receber',
                'fname' => $f['Contas a Receber'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Compras',
                'fname' => $f['Compras'],
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
        Schema::dropIfExists('access_groups');
    }
}
