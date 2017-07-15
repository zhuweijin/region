<?php

namespace App\Libraries\Common;

class Util
{
    static public function getParam($paramArray, $index, $default = '') {
        $value = isset($paramArray[$index]) ? $paramArray[$index] :  $default;
        return $value;
    }
}
