<?php
/**
 *
 */

namespace Crocos\Notifier\Cache;

class Memcached
    implements CacheInterface
{
    protected $memcahced;

    public function __construct($options)
    {
        if (!extension_loaded('memcached')) {
            throw new \RuntimeException("Extension 'memcahced' is required by " . __CLASS__);
        }

        $host = $options['host'];
        $port = (int)$options['port'];

        $memcached = new \Memcached();
        $memcached->addServer($host, $port);

        $this->memcached = $memcached;
    }

    public function get($key)
    {
        $data = $this->memcached->get($key);
        return $data;
    }

    public function set($key, $value, $expier = null)
    {
        $res = $this->memcached->set($key, $value);
        return $res;
    }
}

