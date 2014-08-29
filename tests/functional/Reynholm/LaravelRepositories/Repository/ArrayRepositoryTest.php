<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;

use Reynholm\LaravelRepositories\Repository\ArrayRepository;
use Reynholm\LaravelRepositories\Exception\ColumnNotFoundException;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;

use tests\BaseTests;
use tests\fixtures\UserFixtures;
use tests\repository\UserArrayRepository;

/**
 * Class ArrayRepositoryTest
 * @package tests\unit\Reynholm\LaravelRepositories\Repository
 *
 * @property ArrayRepository $arrayRepository
 * @property UserFixtures $userFixtures
 */
class ArrayRepositoryTest extends BaseTests {


    private $arrayRepository;
    private $userFixtures;

    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase();
        $this->arrayRepository = new UserArrayRepository();
        $this->userFixtures = new UserFixtures();
    }

    public function testType()
    {
        $expectedClass = 'Reynholm\LaravelRepositories\Repository\ArrayRepository';

        $this->assertInstanceOf($expectedClass, $this->arrayRepository);
    }

    public function testFind()
    {
        $this->specify('Find by id', function() {
            expect( $this->arrayRepository->find(1) )->equals( $this->userFixtures->getFixtureId(1) );
        });

        $this->specify('Find by id for a specific column', function() {
            $columns = array('name');
            $expected = array('name' => 'goce');

            expect( $this->arrayRepository->find(1, $columns) )->equals( $expected );
        });

        $this->specify('Find by id for specific columns', function() {
            $columns = array('name', 'age');
            $expected = array('name' => 'goce', 'age' => 30);

            expect( $this->arrayRepository->find(1, $columns) )->equals( $expected );
        });

        $this->specify('Search for an unexistent id returns null', function() {            
            expect( $this->arrayRepository->find(999999) )->equals(null);
            expect( $this->arrayRepository->find(999999, array('name')) )->equals(null);
        });

         /** 
         * @todo Seems to don't work with in memory sqlite database 
         */
        /*$this->specify('Search with a unexistent column throws exception', function() {            
            $this->arrayRepository->find(1, array('this_does_not_exists'));
        }, ['throws' => new ColumnNotFoundException() ] );*/
    }

    public function testFindOrFail()
    {
        $this->specify('Find by id', function() {
            expect( $this->arrayRepository->findOrFail(1) )->equals( $this->userFixtures->getFixtureId(1) );
        });

        $this->specify('Find by id for a specific column', function() {
            $columns = array('name');
            $expected = array('name' => 'goce');

            expect( $this->arrayRepository->findOrFail(1, $columns) )->equals( $expected );
        });

        $this->specify('Find by id for specific columns', function() {
            $columns = array('name', 'age');
            $expected = array('name' => 'goce', 'age' => 30);

            expect( $this->arrayRepository->findOrFail(1, $columns) )->equals( $expected );
        });

        $this->specify('Search unexistent id throws exception', function() {            
            $this->arrayRepository->findOrFail(99999999);
        }, ['throws' => new EntityNotFoundException() ] );
    }

    public function testFindOne()
    {
        $this->markTestIncomplete('To do');
    }

}