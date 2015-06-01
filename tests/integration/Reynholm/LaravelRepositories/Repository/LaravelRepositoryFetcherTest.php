<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use Illuminate\Database\Eloquent\Collection;
use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Repository\LaravelRepository;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;

use Reynholm\LaravelRepositories\Support\Fetcher\CollectionObjectFetcher;
use Reynholm\LaravelRepositories\Support\Fetcher\FetcherInterface;
use Reynholm\LaravelRepositories\Support\Fetcher\MultidimensionalArrayFetcher;
use Reynholm\LaravelRepositories\Support\TableNameGuesser;
use Reynholm\LaravelRepositories\Support\Timestamper;
use tests\BaseTests;
use tests\fixtures\UserFixtures;
use tests\repository\CustomFetcherRepository;
use tests\repository\UserRepository;

/**
 * Class ArrayRepositoryTest
 * @package tests\unit\Reynholm\LaravelRepositories\Repository
 *
 * @property LaravelRepository $arrayRepository
 * @property UserFixtures $userFixtures
 */
class LaravelRepositoryFetcherTest extends BaseTests {

    /** @var  LaravelRepositoryInterface */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new UserRepository(new TableNameGuesser(), new Timestamper());
        $this->prepareDatabase();
    }

    public function testUsingANonExistingFetcherCauseOnAExceptionBeingThrown()
    {
        $this->setExpectedException('\Exception', 'Fetcher not supported: custom-fetcher');

        new CustomFetcherRepository(new TableNameGuesser(), new Timestamper());
    }

    public function testCanChangeFetcherTypeAtRuntime()
    {
        $this->repository->setFetcher(new CollectionObjectFetcher());
        $result = $this->repository->findAll();

        $expected = new Collection([
            (object)['name' => 'goce', 'id' => 1, 'age' => 30],
            (object)['name' => 'morales', 'id' => 2, 'age' => 29],
            (object)['name' => 'silvano', 'id' => 3, 'age' => 28],
        ]);

        $this->assertInstanceOf('Illuminate\Support\Collection', $result);
        $this->assertEquals($expected->toArray(), $result->toArray());
    }

}
