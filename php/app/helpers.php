<?php

if (!function_exists('xabort')) {
    function xabort($statusCode, $code, $message = '', array $headers = [])
    {
        return app()->xabort($statusCode, $code, $message, $headers);
    }
}

if (! function_exists('p')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function p()
    {
        $argvs = func_get_args();
        foreach ($argvs as $v) {
            print_r($v);
            echo PHP_EOL;
        }
    }
}

if (!function_exists('get_cross_domain_headers')) {
    function get_cross_domain_headers($origin)
    {
        $white_domain = config('app.white_domain');

        $allow_headers = 'Origin, X-Requested-With, Content-Type, Accept, X-HTTP-Method-Override, Cookie, AccessToken, AppId';

        if ($origin) {
            $compo = parse_url($origin);
            if (isset($compo['host']) && in_array($compo['host'], $white_domain)) {
                $header = [
                    'Access-Control-Allow-Origin' => $origin,
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Allow-Headers' => $allow_headers,
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, DELETE, PUT'
                ];
                return $header;
            } else {
                app('log')->warn('origin error ' . $origin);
            }
        }
    }
}

if (!function_exists('getLogger')) {
    function getLogger() {
        return app()->getLogger();
    }
}
