<?php

namespace tests\unit\Reynholm\LaravelRepositories\Support\Fetcher;

use Illuminate\Support\Collection;

class CollectionObjectFetcherTest extends \tests\BaseTests {

    /** @var  \Reynholm\LaravelRepositories\Support\Fetcher\CollectionObjectFetcher */
    protected $fetcher;

    public function setUp()
    {
        parent::setUp();

        $this->fetcher = new \Reynholm\LaravelRepositories\Support\Fetcher\CollectionObjectFetcher();
    }

    public function testDataIsFetchedAsObject()
    {
        $data = ['1' => 1];
        $dataAsObject = (object)$data;
        $result = $this->fetcher->fetch($data);

        $this->assertEquals($dataAsObject, $result);
    }

    public function testArrayOfDataIsFetchedAsACollectionOfObjets()
    {
        $data = [['1' => 1]];
        $dataAsObject = new Collection( [(object)$data[0]] );
        $result = $this->fetcher->fetchMany($data);

        $this->assertEquals($dataAsObject->toArray(), $result->toArray());
    }
} 