<?php
/**
 *
 */


namespace Crocos\Notifier\Cache;

interface CacheInterface
{
    public function get($key);
    public function set($key, $value, $expier = null);
}
