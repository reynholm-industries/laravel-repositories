<?php

namespace tests\unit\Reynholm\LaravelRepositories;

use Prophecy\Argument;
use Prophecy\Prophet;
use Reynholm\LaravelRepositories\Behaviour\LaravelRepositoryInterface;
use tests\BaseTests;

class LaravelRepositoryTest extends BaseTests
{
    /** @var  LaravelRepositoryInterface */
    protected $repository;

    /** @var  Prophet */
    protected $prophet;

    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabase();
        $this->repository = \App::make('tests\repository\DownloadRepository');
        $this->prophet = new Prophet();
    }

    public function testFetchIsCalled()
    {
        $fetcherProphecy = $this->prophet->prophesize('Reynholm\LaravelRepositories\Support\Fetcher\FetcherInterface');
        $fetcherProphecy->fetch(Argument::any())->willReturn([1,2,3]);
        $this->repository->setFetcher($fetcherProphecy->reveal());

        $this->assertEquals([1,2,3], $this->repository->find(1));
    }

    public function testFetchManyIsCalled()
    {
        $fetcherProphecy = $this->prophet->prophesize('Reynholm\LaravelRepositories\Support\Fetcher\FetcherInterface');
        $fetcherProphecy->fetchMany(Argument::any())->willReturn([[1,2,3]]);
        $this->repository->setFetcher($fetcherProphecy->reveal());

        $this->assertEquals([[1,2,3]], $this->repository->findMany([['id', '=', 1]]));
    }

} 