<?php

namespace Reynholm\LaravelRepositories\Support;

use Carbon\Carbon;

class Timestamper {

    /**
     * Stamp the current date to the given fields
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function stamp(array $data, array $fields)
    {
        return $this->stampDateToArray($data, $fields, $this->getCurrentDateTime());
    }

    /**
     * @param array $collection
     * @param array $fields
     * @return array
     */
    public function stampCollection(array $collection, array $fields)
    {
        foreach ($collection as &$row) {
            $row = $this->stamp($row, $fields);
        }

        return $collection;
    }

    /**
     * @param array $data
     * @param array $fields
     * @param $date
     * @return array
     */
    private function stampDateToArray(array $data, array $fields, $date)
    {
        foreach ($fields as $field) {
            $data[$field] = $date;
        }

        return $data;
    }

    /**
     * @return string
     */
    private function getCurrentDateTime()
    {
        return Carbon::now()->toDateTimeString();
    }

}