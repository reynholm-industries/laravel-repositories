<?php

namespace tests\unit\Reynholm\LaravelRepositories\Implementation;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;

use Mockery\Mock;

use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
use Reynholm\LaravelRepositories\Implementation\ArrayRepository;
use tests\repository\ExampleArrayRepository;
use tests\unit\BaseUnitTests;

/**
 * Class ArrayRepositoryTest
 * @package tests\unit\Reynholm\LaravelRepositories\Implementation
 * @property ArrayRepository $arrayRepository
 * @property Mock $mockedBuilder
 */
class ArrayRepositoryTest extends BaseUnitTests {

    private $arrayRepository;
    private $mockedBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->mockedBuilder = $this->mock('Illuminate\Database\Query\Builder');
        $this->arrayRepository = new ExampleArrayRepository($this->mockedBuilder);
    }

    public function testType()
    {
        $expectedClass = 'Reynholm\LaravelRepositories\Implementation\ArrayRepository';

        $this->assertInstanceOf($expectedClass, $this->arrayRepository);
    }

    public function testFind()
    {
        verify('Find by id', function() {
            $expected = array(1, 2, 3);
            $this->mockedBuilder->shouldReceive('find')->with(1)->once()->andReturn($expected);

            expect( $this->arrayRepository->find(1) )->equals($expected);
        });

        verify('Find by id for specific columns', function() {
            $columnsToCall = array('name', 'age');
            $expected = array('name' => 'hello', 'age' => 2);

            $this->mockedBuilder->shouldReceive('select')->with($columnsToCall)->once()->andReturn($this->mockedBuilder);
            $this->mockedBuilder->shouldReceive('find')->with(1)->once()->andReturn($expected);
        });

        verify('Find by with unexistent column throws exception', function() {
            $this->mockedBuilder->shouldReceive('select')->with(array('unexistentColumn'))->andThrow(new ColumnNotFoundException());
        }, ['throws' => new ColumnNotFoundException()]);
    }

}