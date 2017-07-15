<?php

namespace App\Services;

class BaseCacheService
{
    const REMEMBER = -1;
    const IGNORE = -2;
    const FORGET = -3;
    const RELOAD = -4;

    const TYPE_JSON = 'json';
    const TYPE_STRING = 'string';

    private $resource;
    private $cacheTTL;
    private $cacheKey;
    private $operation;
    private $dataType;


    public function __construct($resource, $cacheTTL = 60, $cacheKey = null,
                                $operation = self::REMEMBER, $dataType = self::TYPE_JSON)
    {
        $this->resource = $resource;
        $this->cacheTTL = $cacheTTL;
        $this->cacheKey = $cacheKey;
        $this->operation = $operation;
        $this->dataType = $dataType;
    }

    public function __call($method, $arguments)
    {
        if ($this->operation == self::REMEMBER) {
            $cacheKey = $this->generateCacheKey($method, $arguments);
            $cached = $this->getData($cacheKey);
            if (!empty($cached)) {
                return $cached;
            } else {
                $result = call_user_func_array([$this->resource, $method], $arguments);
                $this->saveData($cacheKey, $result);
                return $result;
            }
        } elseif ($this->operation == self::IGNORE) {
            $result = call_user_func_array([$this->resource, $method], $arguments);
            return $result;
        } elseif ($this->operation == self::FORGET) {
            $cacheKey = $this->generateCacheKey($method, $arguments);
            app()->redis->del($cacheKey);
            $result = call_user_func_array([$this->resource, $method], $arguments);
            return $result;
        } elseif ($this->operation == self::RELOAD) {
            $result = call_user_func_array([$this->resource, $method], $arguments);
            $cacheKey = $this->generateCacheKey($method, $arguments);
            $this->saveData($cacheKey, $result);
            return $result;
        }
    }

    public function generateCacheKey($method, $arguments)
    {
        if (isset($this->cacheKey)) {
            return $this->cacheKey;
        }
        $argumentStrList = [];
        foreach ($arguments as $argument) {
            $argumentStrList[] = json_encode($argument);
        }
        $argumentStr = '';
        if (!empty($argumentStrList)) {
            $argumentStr = implode('-', $argumentStrList);
        }
        return "os:service:" . str_replace('\\', '_', strtolower(get_class($this->resource))) . ":{$method}:" . md5($argumentStr);
    }

    private function getData($cacheKey) {
        switch ($this->dataType) {
            case self::TYPE_JSON :
                $value = app()->redis->get($cacheKey);
                if (!empty($value)) {
                    return json_decode($value, true);
                }
                return [];
                break;
            case self::TYPE_STRING :
                return app()->redis->get($cacheKey);
                break;
        }
    }

    private function saveData($cacheKey, $result) {
        if (empty($result)) {
            return false;
        }
        switch ($this->dataType) {
            case self::TYPE_JSON :
                app()->redis->set($cacheKey, json_encode($result));
                break;
            case self::TYPE_STRING :
                app()->redis->set($cacheKey, $result);
                break;
        }
        if ($this->cacheTTL != -1) {
            app()->redis->expire($cacheKey, $this->cacheTTL);
        }
        return true;
    }

    public function setResource($resource) {
        $this->resource = $resource;
        return $this;
    }
}

?>