<?php

namespace tests\fixtures;

class UserFixtures {

    public function getFixtures()
    {
        return array(
            array('id' => 1, 'name' => 'goce',    'age' => 30),
            array('id' => 2, 'name' => 'morales', 'age' => 29),
            array('id' => 3, 'name' => 'silvano', 'age' => 28),
        );
    }

    public function getFixtureId($id)
    {
        return $this->getFixtures()[$id-1];
    }

} 