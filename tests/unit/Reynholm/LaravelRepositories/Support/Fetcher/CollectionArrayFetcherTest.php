<?php

namespace tests\unit\Reynholm\LaravelRepositories\Support\Fetcher;

use Illuminate\Support\Collection;
use Reynholm\LaravelRepositories\Support\Fetcher\CollectionArrayFetcher;

class CollectionArrayFetcherTest extends \tests\BaseTests {

    /** @var  CollectionArrayFetcher */
    protected $fetcher;

    public function setUp()
    {
        parent::setUp();

        $this->fetcher = new CollectionArrayFetcher();
    }

    public function testDataIsFetchedAsArray()
    {
        $data = ['1' => 1];
        $result = $this->fetcher->fetch($data);

        $this->assertEquals($data, $result);
    }

    public function testDataIsFetchedAsACollectionOfArrays()
    {
        $data = new Collection([['1' => 1]]);
        $result = $this->fetcher->fetchMany($data);

        $this->assertNotNull($result);
        $this->assertEquals($data->toArray(), $result->toArray());
    }
} 