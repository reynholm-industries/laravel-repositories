<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;

use Reynholm\LaravelRepositories\Exception\DataNotValidException;
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

    public function testValidate()
    {
        $validData = array('name' => 'carlos', 'age' => 32);
        $invalidData = array('name' => 'goce'); //Name is not unique and age is required

        $this->specify('Returns empty array if no validations where performed', function() {
           expect( $this->arrayRepository->getValidationErrors() )->equals( array() );
        });

        $this->specify('Returns true when data is valid', function() use ($validData) {
            expect_that( $this->arrayRepository->validate($validData) );
        });

        $this->specify('Returns false when data is not valid', function() use ($invalidData) {
            expect_not( $this->arrayRepository->validate($invalidData) );
        });

        //This should be tested in a better way
        $this->specify('Returns validation errors if there are any', function() use ($validData, $invalidData) {
            expect_that( $this->arrayRepository->validate($validData) );
            expect( $this->arrayRepository->getValidationErrors() )->equals( array() );

            expect_not( $this->arrayRepository->validate($invalidData) );
            expect( count($this->arrayRepository->getValidationErrors()['failed'] ) )->equals(2);
        });
    }

    public function testValidateWithCustomRules()
    {
        $nameRequiredRules = ['name' => 'required|min:5|unique:users'];
        $nameAndAgeRequiredRules = ['name' => 'required|min:5|unique:users'];

        //the repository contains a required age constraint so if rules where not overrided it should
        //return false
        $this->specify('Returns true when data is valid', function() use ($nameRequiredRules) {
            expect_that( $this->arrayRepository->validateWithCustomRules(['name' => 'carlos'], $nameRequiredRules) );
        });

        $this->specify('Returns false when data is not valid', function() use ($nameAndAgeRequiredRules) {
            expect_not( $this->arrayRepository->validateWithCustomRules(['age' => 30], $nameAndAgeRequiredRules) );
        });

        $this->specify('Returns validation errors if there are any', function() use ($nameRequiredRules, $nameAndAgeRequiredRules) {
            expect_that( $this->arrayRepository->validateWithCustomRules(['name' => 'carlos'], $nameRequiredRules) );
            expect( $this->arrayRepository->getValidationErrors() )->equals( array() );

            expect_not( $this->arrayRepository->validate(['name' => 'carlos'], $nameAndAgeRequiredRules) );
            expect( count($this->arrayRepository->getValidationErrors()['failed'] ) )->equals(1);
        });
    }

    public function testValidateOrFail()
    {
        $validData   = ['name' => 'carlos', 'age' => 30];
        $invalidData = ['name' => 'carlos'];

        $this->specify('Should return void|null if data is valid', function() use ($validData) {
            expect( $this->arrayRepository->validateOrFail($validData) )->equals(null);
        });

        $this->specify('Should throw exception when data is not valid', function() use ($invalidData) {
            $this->arrayRepository->validateOrFail($invalidData);
        }, ['throws' => new DataNotValidException()]);
    }

    public function testValidateWithCustomRulesOrFail()
    {
        $validData   = ['age' => 30];
        $invalidData = ['name' => 'carlos'];
        $ageRequired = ['age' => 'required'];

        $this->specify('Should return void|null if data is valid', function() use ($validData, $ageRequired) {
            expect( $this->arrayRepository->validateWithCustomRulesOrFail($validData, $ageRequired) )->equals(null);
        });

        $this->specify('Should throw exception when data is not valid', function() use ($invalidData, $ageRequired) {
            expect( $this->arrayRepository->validateWithCustomRulesOrFail($invalidData, $ageRequired) )->equals(null);
        }, ['throws' => new DataNotValidException()]);
    }

    public function testValidateMany()
    {
        $this->markTestIncomplete();
    }

    public function testValidateManyWithCustomRules()
    {
        $this->markTestIncomplete();
    }

    public function testValidateManyOrFail()
    {
        $this->markTestIncomplete();
    }

    public function testValidateManyWithCustomRulesOrFail()
    {
        $this->markTestIncomplete();
    }

}