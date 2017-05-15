<?php

namespace sf\logger;

class Redis
{
    private $redis = null;

    public function __construct($config)
    {
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key, $value, $ttl = 3600)
    {
        return $this->redis->set($key, $value, $ttl);
    }

    public function delete($key1, $key2 = null, $key3 = null )
    {
        return $this->redis->del($key1, $key2, $key3);
    }

    public function rPush($key, $value1, ... $value2)
    {
        return $this->redis->rPush($key, $value1, ... $value2);
    }

    public function lPop($key)
    {
        return $this->redis->lPop($key);
    }

}
