<?php

namespace App\Http\Middleware;

use Closure;
use App\Libraries\Common\RedisKey;
use App\Libraries\Common\Err;

class BeforeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $header = $this->setAllowOrigin($request);
        if ($header) {
            return response('', 204, $header);
        }

        //$this->dropRequest($request);
        $this->isUriForbidden($request);

        $proxies = [
            '127.0.0.1',
            $request->server->get('REMOTE_ADDR'),
        ];
        $request->setTrustedProxies($proxies);

        return $next($request);
    }

    protected function setAllowOrigin($request)
    {
        $origin = $request->header('origin');
        $method = $request->getMethod();
        $headers = [];
        if ($method == 'OPTIONS') {
            $headers = get_cross_domain_headers($origin);
        }
        return $headers;
    }

    protected function isUriForbidden($request)
    {
        $list = config('app.forbidden_uri');
        if (!$list) {
            return;
        }
        $uri = $request->path();
        if (in_array($uri, $list)) {
            xabort(403, Err::ErrServerBusy);
        }
    }

    protected function dropRequest($request)
    {
        $hostname_key = md5(gethostname());
        $key = RedisKey::DROP_REQUEST_RATE . $hostname_key;
        $rate = (int)app('redis')->get($key);
        if ($rate > 0) {
            $rand = mt_rand(0, 100);
            if ($rand <= $rate) {
                xabort(403, Err::ErrServerBusy);
            }
        }
    }
}
