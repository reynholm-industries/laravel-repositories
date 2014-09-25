<?php

namespace tests\unit\Reynholm\LaravelRepositories\Support\Fetcher;

class ArrayObjectFetcherTest extends \tests\BaseTests {

    /** @var  \Reynholm\LaravelRepositories\Support\Fetcher\ArrayObjectFetcher */
    protected $objectFetcher;

    public function setUp()
    {
        parent::setUp();

        $this->objectFetcher = new \Reynholm\LaravelRepositories\Support\Fetcher\ArrayObjectFetcher();
    }

    public function testDataIsFetchedAsObject()
    {
        $data = ['1' => 1];
        $dataAsObject = (object)$data;
        $result = $this->objectFetcher->fetch($data);

        $this->assertEquals($dataAsObject, $result);
    }

    public function testArrayOfDataIsFetchedAsACollectionOfObjets()
    {
        $data = [['1' => 1]];
        $dataAsObject = [(object)$data[0]];
        $result = $this->objectFetcher->fetchMany($data);

        $this->assertEquals($dataAsObject, $result);
    }
} 