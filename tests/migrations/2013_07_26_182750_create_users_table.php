<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
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

        $this->seed();
    }

    public function seed()
    {
        $userFixtures = new \tests\fixtures\UserFixtures();

        DB::table('users')->insert($userFixtures->getFixtures());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}