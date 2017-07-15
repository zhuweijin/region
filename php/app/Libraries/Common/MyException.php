<?php

namespace App\Libraries\Common;

class MyException extends \Exception
{
    public function __construct($errorCode = 0, $message = "") {
        $message = trim($message);
        $msg = empty($message) ? Err::getMsg($errorCode) : Err::getMsg($errorCode) . $message;
        parent::__construct($msg, $errorCode);
    }
}
