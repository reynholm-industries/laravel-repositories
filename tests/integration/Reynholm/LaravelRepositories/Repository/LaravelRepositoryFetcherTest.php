<?php

namespace tests\functional\Reynholm\LaravelRepositories\Repository;

use Reynholm\LaravelRepositories\Exception\DataNotValidException;
use Reynholm\LaravelRepositories\Repository\LaravelRepository;
use Reynholm\LaravelRepositories\Exception\EntityNotFoundException;

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

    public function testUsingANonExistingFetcherCauseOnAExceptionBeingThrowed()
    {
        $this->setExpectedException('\Exception', 'Fetcher not supported: custom-fetcher');

        new CustomFetcherRepository(new TableNameGuesser(), new Timestamper());
    }

}