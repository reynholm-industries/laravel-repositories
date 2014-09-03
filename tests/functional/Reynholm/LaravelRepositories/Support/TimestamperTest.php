<?php

namespace tests\functional\Reynholm\LaravelRepositories\Support;

use Carbon\Carbon;
use DateTime;
use Reynholm\LaravelRepositories\Support\Timestamper;
use tests\BaseTests;

class TimestamperTest extends BaseTests {

    /** @var  Timestamper */
    protected $timestamper;

    public function setUp()
    {
        parent::setUp();

        $this->timestamper = new Timestamper(new Carbon());
    }

    public function testStamp() {

        $this->specify('It stamps the current date', function() {
            $data = ['name' => 'carlos'];

            $stampedData = $this->timestamper->stamp($data, ['created_at', 'updated_at']);
            expect($stampedData)->notEquals($data);
            expect_not( is_null($stampedData['created_at']) );
            expect_not( is_null($stampedData['updated_at']) );
            expect_that($this->validateDate($stampedData['created_at']));
            expect_that($this->validateDate($stampedData['updated_at']));
        });
    }

    public function testStampCollection()
    {
        $data = array(
            ['name' => 'goce'],
            ['name' => 'morales'],
            ['name' => 'silvano'],
        );

        $stampedCollection = $this->timestamper->stampCollection($data, ['created_at', 'updated_at']);

        foreach ($stampedCollection as $stamped) {
            expect($stamped)->notEquals($data);
            expect_not( is_null($stamped['created_at']) );
            expect_not( is_null($stamped['updated_at']) );
            expect_that($this->validateDate($stamped['created_at']));
            expect_that($this->validateDate($stamped['updated_at']));
        }
    }

    private function validateDate($date)
    {
        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

} 