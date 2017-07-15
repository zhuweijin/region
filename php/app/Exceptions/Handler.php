<?php

namespace App\Exceptions;

use Exception;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Libraries\Common\Err;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (strpos($e->getMessage(), 'SOAP-ERROR') !== false) {
            return false;
        }
        
        if (method_exists($e, 'getStatusCode')) {
            $status_code = (int)$e->getStatusCode();
            if ($status_code < 500 || !env('APP_DEBUG')) {
                return $this->renderJson($request, $e);
            }
        }

        return parent::render($request, $e);
    }

    protected function renderJson($request, Exception $e)
    {
        $status_code = (int)$e->getStatusCode();
        $result = [
            'code' => $e->getCode(),
            'msg' => $e->getMessage() ?: Err::getMsg($e->getCode()),
        ];
        if ($status_code >= 500) {
            $result['code'] = Err::ErrInternalServer;
            $result['msg'] = Err::getMsg(Err::ErrInternalServer);
        } elseif ($status_code == 404 || $status_code == 405) {
            $result['code'] = Err::ErrInvalidRequest;
            $result['msg'] = Err::getMsg(Err::ErrInvalidRequest);
        }
        //if (!env('APP_DEBUG')) {
        //    unset($result['msg']);
        //}
        $headers = $this->setAllowOrigin($request);
        $headers = $headers ?: [];
        $this->logException($e, $request);
        return response($result, $status_code, $headers);
    }

    protected function setAllowOrigin($request)
    {
        $origin = $request->header('origin');
        $headers = get_cross_domain_headers($origin);
        return $headers;
    }

    protected function logException($e, $request)
    {
        $code = $e->getCode();
        $trace = [];
        foreach ($e->getTrace() as $v) {
            if (isset($v['file']) && (strpos($v['file'], 'vendor/') === false &&
                    strpos($v['file'], 'Middleware/') === false)) {
                $trace[] = $v;
            }
        }
        app('log')->warn("error code {$code} trace ", $trace);
    }
}
