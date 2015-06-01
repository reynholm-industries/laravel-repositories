<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Repository\LaravelRepository;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;

use tests\BaseTests;
use tests\fixtures\UserFixtures;
use tests\repository\UserRepository;

/**
 * Class ArrayRepositoryTest
 * @package tests\unit\Reynholm\LaravelRepositories\Repository
 *
 * @property LaravelRepository $arrayRepository
 * @property UserFixtures $userFixtures
 */
class LaravelRepositoryTest extends BaseTests {


    private $arrayRepository;
    private $userFixtures;

    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase();
        $this->arrayRepository = \App::make('tests\repository\UserRepository');
        $this->userFixtures = new UserFixtures();
    }

    public function testType()
    {
        $expectedClass = 'Reynholm\LaravelRepositories\Repository\LaravelRepository';

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

    public function testFindMany()
    {
        $this->specify('Can find all rows looking for fields', function() {
            expect( $this->arrayRepository->findMany([ ['age', '>', 1] ]) )->equals( $this->userFixtures->getFixtures() );
            expect( $this->arrayRepository->findMany([ ['age', '>', 1], ['name', '<>', 'hacker'] ]) )->equals( $this->userFixtures->getFixtures() );
        });

        $this->specify('Can limit the query results', function() {
            expect( $this->arrayRepository->findMany([ ['age', '>', 1] ], array(), 1) )->equals( [$this->userFixtures->getFixtures()[0]] );
            expect( $this->arrayRepository->findMany([ ['age', '>', 1], ['name', '<>', 'hacker'] ], array(), 3) )->equals( $this->userFixtures->getFixtures() );
        });

        $this->specify('Can order by any field, filtered and limited', function() {
            expect( $this->arrayRepository->findMany([ ['age', '>', 1] ], array('name'), 1, ['name' => 'desc']) )->equals( [['name' => 'silvano']] );
        });

        $this->specify('If there are no results returns an empty array', function() {
            expect( $this->arrayRepository->findMany([['name', '=', 'unexistent-name']]) )->equals([]);
        });
    }

    public function testFindAll()
    {
        $this->specify('Can return all of the data of a table', function () {
            expect($this->arrayRepository->findAll())->equals($this->userFixtures->getFixtures());
        });

        $this->specify('Can return all of the data with custom columns', function() {
            $expected = array(['name' => 'goce'], ['name' => 'morales'], ['name' => 'silvano']);

            expect($this->arrayRepository->findAll(['name']))->equals($expected);
        });

        $this->specify('Can return all of the data limited', function() {
            $expected1 = [$this->userFixtures->getFixtureId(1)];
            $expected2 = [$this->userFixtures->getFixtureId(1), $this->userFixtures->getFixtureId(2)];

            expect($this->arrayRepository->findAll(['id', 'name', 'age'], 1))->equals($expected1);
            expect($this->arrayRepository->findAll(['id', 'name', 'age'], 2))->equals($expected2);
        });
    }

    public function testLists()
    {
        $this->specify('Can list with the given name and key', function() {
            $expected = [1 => 'goce', 2 => 'morales', 3 => 'silvano'];

            expect( $this->arrayRepository->lists('name', 'id') )->equals($expected);
        });
    }

    public function testCreate()
    {
        $validResource   = ['name' => 'hello', 'age' => 25];
        $invalidResource = ['name' => 'kk', 'age' => 1];

        $this->specify('Resource is created if valid', function() use ($validResource) {
            $expected = ['id' => 4, 'name' => 'hello', 'age' => 25];
            expect_that($this->arrayRepository->create($validResource));
            expect($this->arrayRepository->findOne([['name', '=', 'hello']]))->equals($expected);
        });

        $this->specify('Resource is not created if not valid', function() use ($invalidResource) {
            $this->arrayRepository->create($invalidResource);
        }, ['throws' => new DataNotValidException()]);

        $this->specify('Resource is created if force is specified', function() use ($invalidResource) {
            expect_that($this->arrayRepository->create($invalidResource, true));
        });
    }

    public function testCreateMany()
    {
        $validResources   = array(
            ['name' => 'travis', 'age' => 2],
            ['name' => 'tester', 'age' => 10],
            ['name' => 'coverager', 'age' => 3],
        );

        $invalidResources = array(
            ['name' => 'travis', 'age' => 2],
            ['age' => 10],
            ['name' => 'coverager'],
        );

        $this->specify('Resources are created if valid', function() use ($validResources) {
            expect_that($this->arrayRepository->createMany($validResources));
            expect($this->arrayRepository->count())->equals(6);
        });

        $this->specify('Resources are not created if not valid', function() use ($invalidResources) {
            $this->arrayRepository->createMany($invalidResources);
        }, ['throws' => new DataNotValidException()]);
    }

    public function testCount()
    {
        $this->specify('Can count all rows', function() {
            expect($this->arrayRepository->count())->equals(3);
        });

        $this->specify('Can count rows by criteria', function() {
            $criteria = [['name', '=', 'goce']];
            expect($this->arrayRepository->count($criteria))->equals(1);
        });
    }

    public function testUpdate()
    {
        $this->specify('Can update a resource by its id', function() {
            expect($this->arrayRepository->update(1, ['name' => 'charly', 'age' => '32']) )->equals(1);
            expect($this->arrayRepository->findOrFail(1)['name'] )->equals('charly');
            expect($this->arrayRepository->findOrFail(1)['age'] )->equals(32);
        });

        $this->specify('Update a non existent resource returns 0', function() {
            expect($this->arrayRepository->update(99999, ['name' => 'charly']))->equals(0);
        });
    }

    public function testUpdateMany()
    {
        $this->specify('Can update one or more resources at once', function() {
            expect($this->arrayRepository->updateMany([['age', '>', 0]], ['age' => 3]))->equals(3);

            $allUsers = $this->arrayRepository->findAll();

            foreach ($allUsers as $user) {
                expect($user['age'])->equals(3);
            }

            expect($this->arrayRepository->updateMany([['name', '=', 'goce']], ['age' => 2]))->equals(1);
            expect($this->arrayRepository->findOrFail(1)['name'])->equals('goce');
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

    public function testDeleteAll()
    {
        $this->specify('Delete all the data of the table', function() {
            expect($this->arrayRepository->count())->equals(3);
            expect( $this->arrayRepository->deleteAll() )->equals(3);
            expect($this->arrayRepository->count())->equals(0);
            expect( $this->arrayRepository->deleteAll() )->equals(0);
        });
    }

    public function testValidate()
    {
        $validData = array('name' => 'carlos', 'age' => 32);
        $invalidData = array('name' => 'goce'); //Name is not unique and age is required

        $this->specify('Returns empty array if no validations where performed', function() {
           expect( $this->arrayRepository->getValidationErrors() )->equals( array() );
           expect( $this->arrayRepository->getValidationMessages() )->equals( array() );
           expect( $this->arrayRepository->getValidationFailures() )->equals( array() );
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

            $errors = $this->arrayRepository->getValidationErrors();            
            expect( count($errors['messages']) )->equals(2);
            expect( count($errors['failed']) )->equals(2);
            expect(count($this->arrayRepository->getValidationMessages()))->equals(2);
            expect(count($this->arrayRepository->getValidationFailures()))->equals(2);
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

        $this->specify('The errors should be saved anyway even if exception is thrown', function() use ($invalidData, $ageRequired) {

            try {
                $this->arrayRepository->validateWithCustomRulesOrFail($invalidData, $ageRequired);
            }
            catch(DataNotValidException $e) {

            }

            expect( count($this->arrayRepository->getValidationErrors()['failed']) )->equals(1);
        });
    }

    public function testValidateMany()
    {
        $validData = array(
            ['name' => 'carlos', 'age' => 50],
            ['name' => 'freddy', 'age' => 39],
            ['name' => 'ricky',  'age' => 70],
        );

        $invalidData = array(
            ['name' => 'carlos',],
            ['name' => 'freddy', 'age' => 39],
            ['age' => 70],
        );

        $this->specify('Empty arrays are returned when there are no errors', function()  {
            expect($this->arrayRepository->getValidationErrors())->equals([]);
            expect($this->arrayRepository->getValidationMessages())->equals([]);
            expect($this->arrayRepository->getValidationFailures())->equals([]);
        });

        $this->specify('Returns true when data is valid and there are no errors',
            function() use ($validData)  {
                expect_that($this->arrayRepository->validateMany($validData));
        });

        $this->specify('Returns false when data is not valid and errors are written',
            function() use ($invalidData) {                
                expect_not($this->arrayRepository->validateMany($invalidData));
                expect(count($this->arrayRepository->getValidationErrors()))->equals(2);                

                //carlos failures
                expect(count($this->arrayRepository->getValidationErrors()[0]['messages']))->equals(1);
                expect(count($this->arrayRepository->getValidationErrors()[0]['failed']))->equals(1);
                expect(count($this->arrayRepository->getValidationMessages()))->equals(2);
                expect(count($this->arrayRepository->getValidationFailures()))->equals(2);                

                //no name failures
                expect(count($this->arrayRepository->getValidationErrors()[1]['messages']))->equals(1);
                expect(count($this->arrayRepository->getValidationErrors()[1]['failed']))->equals(1);
        });
    }

    public function testValidateManyWithCustomRules()
    {
        $validData = array(
            ['name' => 'carlos', 'age' => 50],
            ['name' => 'freddy', 'age' => 39],
            ['name' => 'ricky',  'age' => 70],
        );

        $invalidData = array(
            ['name' => 'carlos',],
            ['name' => 'freddy', 'age' => 39],
            ['age' => 70],
        );

        $rules = array(
            'name' => 'required|min:5|unique:users',
            'age'  => 'required|integer|between:0,120',
        );

        $this->specify('Returns true when data is valid and there are no errors',
            function() use ($validData, $rules)  {
            expect_that($this->arrayRepository->validateManyWithCustomRules($validData, $rules));
        });

        $this->specify('Returns false when data is not valid and errors are written',
            function() use ($invalidData, $rules) {
            expect_not($this->arrayRepository->validateManyWithCustomRules($invalidData, $rules));
            expect(count($this->arrayRepository->getValidationErrors()))->equals(2);
        });
    }

    public function testValidateManyOrFail()
    {
        $validData = array(
            ['name' => 'carlos', 'age' => 50],
            ['name' => 'freddy', 'age' => 39],
            ['name' => 'ricky',  'age' => 70],
        );

        $invalidData = array(
            ['name' => 'carlos',],
            ['name' => 'freddy', 'age' => 39],
            ['age' => 70],
        );

        $this->specify('If data is valid nothing should happen', function() use($validData) {
            expect( $this->arrayRepository->validateManyOrFail($validData) )->equals(null);
        });

        $this->specify('If data is not valid throw exception', function() use($invalidData) {
            $this->arrayRepository->validateManyOrFail($invalidData);
        }, ['throws' => new DataNotValidException()]);

    }

    public function testValidateManyWithCustomRulesOrFail()
    {
        $validData = array(
            ['name' => 'carlos', 'age' => 50],
            ['name' => 'freddy', 'age' => 39],
            ['name' => 'ricky',  'age' => 70],
        );

        $invalidData = array(
            ['name' => 'carlos',],
            ['name' => 'freddy', 'age' => 39],
            ['age' => 70],
        );

        $rules = array(
            'name' => 'required|min:5|unique:users',
            'age'  => 'required|integer|between:0,120',
        );

        $this->specify('Returns nothing when data is valid',
            function() use ($validData, $rules)  {
                expect($this->arrayRepository->validateManyWithCustomRulesOrFail($validData, $rules))->equals(null);
        });

        $this->specify('Throws exception when data is not valid',
            function() use ($invalidData, $rules) {
            $this->arrayRepository->validateManyWithCustomRulesOrFail($invalidData, $rules);
        }, ['throws' => new DataNotValidException()] );

        $this->specify('The errors should be saved anyway even if exception is thrown', function() use ($invalidData, $rules) {

            try {
                $this->arrayRepository->validateManyWithCustomRulesOrFail($invalidData, $rules);
            }
            catch(DataNotValidException $e) {

            }

            expect( count($this->arrayRepository->getValidationErrors()) )->equals(2);
        });
    }

}
