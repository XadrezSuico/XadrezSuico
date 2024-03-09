<?php
namespace App\Helper;

use App\Helper\Singleton\SingletonValue;

class SingletonValueHelper
{
    private static $values = [];

    public static function getInstance($id)
    {
        if (!isset($values[$id])) {
            self::$values[$id] = new SingletonValue;
        }
        return self::$values[$id];
    }

    public static function has($id)
    {
        if (!isset($values[$id])) {
            return false;
        }
        return true;
    }
}
