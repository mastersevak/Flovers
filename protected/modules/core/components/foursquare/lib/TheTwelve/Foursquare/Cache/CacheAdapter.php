<?php

namespace TheTwelve\Foursquare\Cache;

use TheTwelve\Foursquare\Cache;

class CacheAdapter implements Cache
{
    private $cache;
    
    public function __construct($cache)
    {
        $this->cache = $cache;
    }
    
    public function get($key)
    {
        return call_user_func_array(array($this->cache, 'get'), func_get_args());
    }

    public function set($key, $response, $expire, $dependency)
    {
        return call_user_func_array(array($this->cache, 'set'), func_get_args());
    }
    
    public function __call($name, $parameters)
    {
        if(!method_exists($this->cache, $name)) {
            throw new \RuntimeException('Call undefined method [' . $name . ']');
        }
        
        return call_user_func_array(array($this->cache, $name), $parameters);
    }
}
