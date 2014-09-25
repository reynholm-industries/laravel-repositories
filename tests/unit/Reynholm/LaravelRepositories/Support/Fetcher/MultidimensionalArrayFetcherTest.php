<?php

namespace tests\unit\Reynholm\LaravelRepositories\Support\Fetcher;

class MultidimensionalArrayFetcher extends \tests\BaseTests {

    /** @var  \Reynholm\LaravelRepositories\Support\Fetcher\MultidimensionalArrayFetcher() */
    protected $objectFetcher;

    public function setUp()
    {
        parent::setUp();

        $this->objectFetcher = new \Reynholm\LaravelRepositories\Support\Fetcher\MultidimensionalArrayFetcher();
    }

    public function testDataIsFetchedAsObject()
    {
        $data = ['1' => 1];
        $result = $this->objectFetcher->fetch($data);

        $this->assertEquals($data, $result);
    }

    public function testArrayOfDataIsFetchedAsACollectionOfObjets()
    {
        $data = [['1' => 1]];
        $dataAsObject = [$data[0]];
        $result = $this->objectFetcher->fetchMany($data);

        $this->assertEquals($dataAsObject, $result);
    }
} 