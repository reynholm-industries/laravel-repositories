<?php

namespace Reynholm\LaravelRepositories\Support\Fetcher;

/**
 * Class ArrayObjectFetcher
 * @package Reynholm\LaravelRepositories\Support\Fetcher
 *
 * Returns data as a collection of objects
 */
class ArrayObjectFetcher implements FetcherInterface
{
    public function fetch($data)
    {
        return (object)$data;
    }

    public function fetchMany($data)
    {
        $result = [];

        foreach ($data as $row) {
            $result[] = (object) $row;
        }

        return $result;
    }
}