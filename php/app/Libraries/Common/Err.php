<?php

namespace App\Libraries\Common;

class Err
{
    const ErrOk                             = 0;
    const ErrInvalidRequest                 = 40000;
    const ErrInvalidSession                 = 40001;
    const ErrInvalidParam                   = 40002;
    const ErrMissingParam                   = 40003;
    const ErrInternalServer                 = 50000;
    const ErrServerBusy                     = 50001;
    const ErrDatabaseServer                 = 60000;

    protected static $msg = [
        '0'     => 'success',
        '40000' => '错误的请求地址或方法',
        '40001' => '非法会话或会话已过期',
        '40002' => '非法参数',
        '40003' => '参数缺失',
        '50000' => '内部服务器错误',
        '50001' => '服务器正忙',
        '60000' => '数据库错误',
    ];

    public static function getMsg($code) {
        $str_code = (string)$code;
        return isset(self::$msg[$str_code]) ? self::$msg[$str_code] : '';
    }

    public static function getErrorCodeArray() {
        return self::$msg;
    }
}
