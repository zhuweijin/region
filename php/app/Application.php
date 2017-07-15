<?php 

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Db;

class Application extends \Laravel\Lumen\Application
{
    public function __construct($basePath = null, $storagePath = null)
    {
        date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Shanghai'));

        $this->basePath = $basePath;
        $this->storagePath = $storagePath;
        $this->bootstrapContainer();
        $this->registerErrorHandling();
    }

    public function xabort($statusCode, $code = 0, $message = '', array $headers = [])
    {
        if ($statusCode == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($statusCode, $message, null, $headers, $code);
    }

    protected function getMonologHandler()
    {
        $file_name = date('Ymd') . '.log';
        $file_path = storage_path('logs/' . $file_name);
        return (new StreamHandler($file_path, Logger::DEBUG))
                            ->setFormatter(new LineFormatter(null, null, true, true));
    }

    public function getLogger() {
        return app('Psr\Log\LoggerInterface');
    }



}
