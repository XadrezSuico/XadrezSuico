<?php


namespace App\Helper\Singleton;

class SingletonValue{
    private $value;

    public function get()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }
}
