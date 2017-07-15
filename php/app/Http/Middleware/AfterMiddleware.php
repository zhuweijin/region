<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Collection;

class AfterMiddleware
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
        $response = $next($request);

        // Do it
        $this->setAllowOrigin($request, $response);
        //$this->addServerTime($request, $response);

        return $response;
    }

    protected function setAllowOrigin($request, $response)
    {
        $origin = $request->header('origin');
        $headers = get_cross_domain_headers($origin);
        if ($headers) {
            foreach ($headers as $k => $v) {
                $response->header($k, $v);
            }
        }
    }

    protected function addServerTime($request, $response)
    {
        $content = $response->getOriginalContent();
        $method = $request->getMethod();
        if (env('APP_ENV') == 'testing' || $method == 'OPTIONS') {
            return;
        }
        if (!$content) {
            return;
        }

        $is_collection = $content instanceof Collection;
        if ($is_collection) {
            return;
        }

        $type = gettype($content);

        $is_string = $type == 'string';
        if ($is_string) {
            return;
        }

        $is_array = $type == 'array';

        if ($is_array && isset($content[0])) {
            return;
        }

        $content['server_time'] = time();
        $response->setContent($content);
    }
}
