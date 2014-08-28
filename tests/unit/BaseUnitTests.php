<?php

namespace tests\unit;

use \Mockery as m;
use Prophecy\PhpUnit\ProphecyTestCase;
use Codeception\Specify;

abstract class BaseUnitTests extends ProphecyTestCase {

    use Specify;

    /**
     * Returns a mocked instance of the given class
     * @param $class
     * @return m\MockInterface
     */
    protected function mock($class) {
        return m::mock($class);
    }

    protected function tearDown()
    {
        m::close();
    }

} 