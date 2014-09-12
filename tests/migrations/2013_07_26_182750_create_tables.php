<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('age');
        });

        Schema::create('downloads', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        $this->seed();
    }

    public function seed()
    {
        $userFixtures = new \tests\fixtures\UserFixtures();

        DB::table('users')->insert($userFixtures->getFixtures());
        DB::table('downloads')->insert(
            array(
                ['user_id' => 1, 'created_at' => '2014-09-03 08:09:47', 'updated_at' => '2014-09-03 08:09:47'],
                ['user_id' => 2, 'created_at' => '2014-09-03 08:09:47', 'updated_at' => '2014-09-03 08:09:47'],
                ['user_id' => 3, 'created_at' => '2014-09-03 08:09:47', 'updated_at' => '2014-09-03 08:09:47'],
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('downloads');
        Schema::drop('users');
    }
}