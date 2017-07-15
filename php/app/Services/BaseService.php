<?php 
namespace App\Services;

use App\Libraries\Common\RedisKey;

class BaseService implements RedisKey
{

    protected $redis;

    public function __construct()
    {
         $this->redis = app()->redis;
    }

    public function remember($cacheTTL = 60, $cacheKey = null, $dataType = BaseCacheService::TYPE_JSON)
    {
        return new BaseCacheService($this, $cacheTTL, $cacheKey, BaseCacheService::REMEMBER, $dataType);
    }

    public function reload($cacheTTL = 60, $cacheKey = null, $dataType = BaseCacheService::TYPE_JSON)
    {
        return new BaseCacheService($this, $cacheTTL, $cacheKey, BaseCacheService::RELOAD, $dataType);
    }

    public function forget($cacheKey = null)
    {
        return new BaseCacheService($this, 0, $cacheKey, BaseCacheService::FORGET);
    }

    public function ignore($cacheKey = null)
    {
        return new BaseCacheService($this, 0, $cacheKey, BaseCacheService::IGNORE);
    }
}
