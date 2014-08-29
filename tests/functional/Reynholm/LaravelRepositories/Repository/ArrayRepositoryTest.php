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
            expect( $this->arrayRepository->find(999999) )->equals([]);
            expect( $this->arrayRepository->find(999999, array('name')) )->equals([]);
        });

         /** 
         * @todo Seems to don't work with in memory sqlite database. look for a workaround
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
        $this->specify('Find one by 1 criteria', function() {
            $criteria = [ ['name', '=', 'goce'] ];

            expect( $this->arrayRepository->findOne($criteria) )->equals( $this->userFixtures->getFixtureId(1) );
        });

        $this->specify('Find one by 2 criterias', function() {
            $criteria = [ ['name', '=', 'goce'], ['age', '>', 20] ];

            expect( $this->arrayRepository->findOne($criteria) )->equals( $this->userFixtures->getFixtureId(1) );
        });

        $this->specify('Find one with specific columns', function() {
            $criteria = [ ['name', '=', 'goce'] ];

            expect( $this->arrayRepository->findOne($criteria, array('name')) )->equals( array('name' => 'goce') );
        });

        $this->specify('Find one when entity is not found returns an empty array', function() {
            $criteria = [ ['name', '=', 'user-that-does-not-exists'] ];
            expect( $this->arrayRepository->findOne($criteria) )->equals([]);
        });
    }

    public function testDelete()
    {
        $this->specify('Delete one entity', function() {
            expect_that($this->arrayRepository->delete(1));
        });

        $this->specify('Delete unexistent entity returns false', function() {
            expect_not($this->arrayRepository->delete(99999));
        });
    }

    public function testDeleteOrFail()
    {
        $this->specify('Delete one entity does not throw exception', function() {
            $this->arrayRepository->deleteOrFail(1);
        });

        $this->specify('Delete unexistent entity throws exception', function() {
            $this->arrayRepository->deleteOrFail(99999);
        }, ['throws' => new EntityNotFoundException()]);
    }

}