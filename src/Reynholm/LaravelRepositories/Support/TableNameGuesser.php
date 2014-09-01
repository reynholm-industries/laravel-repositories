<?php

namespace Reynholm\LaravelRepositories\Support;

class TableNameGuesser
{
    /**
     * Try to guess the table name based on the repository class name
     * just like Laravel guess the table based on the model class name
     * @param String $className
     * @return string
     */
    public function guess($className)
    {
        $className = $this->removeNamespace($className);
        return $this->pluralize(preg_replace("@(repository|Repository)@", "", $className));
    }

    /**
     * @param $string
     * @return string
     */
    private function pluralize($string) {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string)) . 's';
    }

    /**
     * @param string $className
     * @return string
     */
    private function removeNamespace($className)
    {
        return last( explode('\\', $className) );
    }

} 