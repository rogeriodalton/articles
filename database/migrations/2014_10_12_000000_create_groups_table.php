<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name');
            $table->string('fname');
            $table->timestamps();
        });

        $f = [
            'Administração' => phonetics('Administração'),
            'Vendas' => phonetics('Vendas'),
            'Contas à Pagar' => phonetics('Contas à Pagar'),
            'Contas à Receber' => phonetics('Contas à Receber'),
            'Compras' => phonetics('Compras'),
            'Suporte técnico' => phonetics('Suporte técnico'),
        ];

        DB::table('groups')->insert([
            [
                'name' => 'Administração',
                'fname' => $f['Administração'],
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
                'name' => 'Contas à Pagar',
                'fname' => $f['Contas à Pagar'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Contas à Receber',
                'fname' => $f['Contas à Receber'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Compras',
                'fname' => $f['Compras'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Suporte técnico',
                'fname' => $f['Suporte técnico'],
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
        Schema::dropIfExists('groups');
    }
}
