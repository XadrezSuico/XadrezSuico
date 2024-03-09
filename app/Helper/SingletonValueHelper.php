<?php
namespace App\Helper;

use App\Helper\Singleton\SingletonValue;
use Illuminate\Support\Facades\Log;

class SingletonValueHelper
{
    private static $instance = null;

    private $values = [];

    public static function getInstance()
    {
        if (self::$instance == null) {
            Log::debug("generating SingletonValueHelper instance");
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function has($id)
    {
        return isset($this->values[$id]);
    }

    public function get($id)
    {
        return $this->values[$id];
    }

    public function set($id,$value)
    {
        $this->values[$id] = $value;
    }
}
