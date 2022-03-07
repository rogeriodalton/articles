<?php
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB,Hash};

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name');
            $table->string('fname')->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('active')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        $f = [
            'salesman' => phonetics('salesman'),
            'administrator' => phonetics('administrator'),
        ];

        DB::table('users')->insert([
            [
                'name' => 'administrator',
                'fname' => $f['administrator'],
                'email' => 'admin@email.com',
                'password' => Hash::make('admpassword'),
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'salesman',
                'fname' => $f['salesman'],
                'email' => 'salesman@email.com',
                'password' => Hash::make('mypassword'),
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
        Schema::dropIfExists('users');
    }
}
