<?php

namespace App\Libraries\Common;

use Illuminate\Redis\Database;
use Illuminate\Support\Arr;

class RedisLock
{
    const TTL = 2;

    protected $key = '';
    protected $val = '';
    protected $redis = '';

    public function __construct($key)
    {
        $this->key = RedisKey::REDIS_LOCK . $key;
        $this->val = 0;
        $servers = config('database.redis');
        Arr::pull($servers, 'cluster');
        Arr::pull($servers, 'options');
        $this->redis = new Database($servers);
    }

    public function lock()
    {
        $script = $this->getLockScript();
        $this->val = time();
        try {
            $res = $this->redis->evalsha(sha1($script), 1, $this->key, $this->val, self::TTL);
        } catch (\Exception $e) {
            $res = $this->redis->eval($script, 1, $this->key, $this->val, self::TTL);
        }
        return $res == 1;
    }

    public function unlock()
    {
        $script = $this->getUnLockScript();
        try {
            $res = $this->redis->evalsha(sha1($script), 1, $this->key, $this->val);
        } catch (\Exception $e) {
            $res = $this->redis->eval($script, 1, $this->key, $this->val);
        }
        return $res == 1;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getVal()
    {
        return $this->val;
    }

    protected function getLockScript()
    {
        $script = <<<LUA
local key = KEYS[1]
local value = ARGV[1]
local TTL = ARGV[2]
local is_set = redis.call('setnx', key, value)
if is_set == 1 then
    redis.call('expire', key, TTL)
    return 1
else
    return 0
end
LUA;
        return $script;
    }

    protected function getUnLockScript()
    {
        $script = <<<LUA
local key = KEYS[1]
local value = ARGV[1]
if redis.call('get', key) == value then
    return redis.call('del', key)
else
    return 0
end
LUA;
        return $script;
    }
}
