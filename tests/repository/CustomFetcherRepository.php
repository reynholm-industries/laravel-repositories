<?php

namespace tests\repository;

use Reynholm\LaravelRepositories\Repository\LaravelRepository;

/**
 * Class CustomFetcherRepository
 * @package tests\repository
 *
 * This repository is to check if a exception is trowed when invalid fetcher is given
 */
class CustomFetcherRepository extends LaravelRepository {
    protected $fetchMode = 'custom-fetcher';
} 