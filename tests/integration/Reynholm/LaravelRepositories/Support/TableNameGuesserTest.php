<?php

namespace tests\functional\Reynholm\LaravelRepositories\Support;

use Reynholm\LaravelRepositories\Support\TableNameGuesser;
use tests\BaseTests;

class TableNameGuesserTest extends BaseTests {

    /** @var  TableNameGuesser */
    protected $guesser;

    public function setUp()
    {
        parent::setUp();

        $this->guesser = new TableNameGuesser();
    }

    public function testGuess() {

        $this->specify('Can guess table name', function() {

            $useCases = array(
                'UserRepository'        => 'users',
                'User'                  => 'users',
                'InvoiceRowRepository'  => 'invoice_rows',
                'ProductBrand'          => 'product_brands',
                'CustomerHistoryLog'    => 'customer_history_logs',

                //Namespaces are removed aswell
                'Reynholm\Repository\UserRepository' => 'users',
                'Reynholm\\Repository\\Product'      => 'products',
            );

            foreach ($useCases as $useCase => $expected) {
                expect($this->guesser->guess($useCase))->equals($expected);
            }

        });
    }

} 