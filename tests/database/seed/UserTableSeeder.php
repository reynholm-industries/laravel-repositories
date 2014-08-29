<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {

	protected $dateNow;

	public function __construct()
	{	
		$this->dateNow = Carbon\Carbon::now();
	}

    public function run()
    {
        \DB::table('users')->insert(
            array(
                array('name' => 'Goce',    'created_at' => $this->dateNow, 'updated_at' => $this->dateNow),
                array('name' => 'Morales', 'created_at' => $this->dateNow, 'updated_at' => $this->dateNow),
                array('name' => 'Silvano', 'created_at' => $this->dateNow, 'updated_at' => $this->dateNow),
            )
        );

    }

}