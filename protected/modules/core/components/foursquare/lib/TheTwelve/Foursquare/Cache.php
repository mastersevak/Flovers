<?php

namespace TheTwelve\Foursquare;

interface Cache
{
    public function set($key, $response, $expire, $dependency);
    
    public function get($key);
}
