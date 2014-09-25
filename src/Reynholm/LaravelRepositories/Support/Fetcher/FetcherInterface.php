<?php

namespace Reynholm\LaravelRepositories\Support\Fetcher;

interface FetcherInterface {
    public function fetch($data);
    public function fetchMany($data);
} 