<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use DateTime;
use Reynholm\LaravelRepositories\Repository\LaravelRepository;

use tests\BaseTests;
use tests\fixtures\UserFixtures;

/**
 * Class ArrayRepositoryTimestampsTest
 *
 * DownloadRepository contains timestampable property
 * as true so it should adds timestamps
 *
 * @package tests\unit\Reynholm\LaravelRepositories\Repository
 *
 * @property LaravelRepository $arrayRepository
 * @property UserFixtures $userFixtures
 */
class ArrayRepositoryTimestampsTest extends BaseTests {

    private $arrayRepository;

    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase();
        $this->arrayRepository = \App::make('tests\repository\DownloadRepository');
    }

    public function testCreate()
    {
        expect($this->arrayRepository->count())->equals(3);
        expect_that($this->arrayRepository->create(['user_id' => 1]) );
        expect($this->arrayRepository->count())->equals(4);

        $created = last($this->arrayRepository->findAll());
        expect_that($this->validateDatetime($created['created_at']));
        expect_that($this->validateDatetime($created['updated_at']));
    }

    public function testCreateMany()
    {
        $dataToCreate = array(
            ['user_id' => 1], ['user_id' => 2], ['user_id' => 3],
        );

        expect($this->arrayRepository->count())->equals(3);
        expect_that($this->arrayRepository->createMany($dataToCreate) );
        expect($this->arrayRepository->count())->equals(6);

        $newCreatedData = $this->arrayRepository->findAll([], 3, ['id' => 'desc']);

        foreach ($newCreatedData as $created) {
            expect_that($this->validateDatetime($created['created_at']));
            expect_that($this->validateDatetime($created['updated_at']));
        }
    }

    public function testUpdate()
    {
        $old = $this->arrayRepository->find(1);
        expect($this->arrayRepository->update(1, ['user_id' => 1]))->equals(true);
        $new = $this->arrayRepository->find(1);

        expect($old)->notEquals($new);
        expect_that($this->validateDatetime($new['updated_at']));
    }

    private function validateDatetime($date)
    {
        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

}