<?php
/**
 *
 */

namespace Crocos\Notifier;

use Crocos\Notifier\Cache\CacheInterface;
use Crocos\Notifier\Notifier\NotifierInterface;

class Notifier
{
    const LOG_ERROR = 0x0001;
    const LOG_DEBUG = 0xffff;

    protected $config = array();
    protected $cache = null;
    protected $notifier = null;

    public function __construct(array $config)
    {
        $this->notifier = new \SplObjectStorage();
        $this->config = $config;
        $this->log_level = @$config['log_level'] ?: 0;

        if (isset($config['cache'])) {
            $this->setCache($this->getCache($config['cache']));
        }
        foreach ($config['notify'] as $notify => $options) {
            $this->notifier->attach($this->getNotifier($notify, $options));
        }
    }

    public function message($id, $value, $use_cache = true)
    {
        foreach ($this->notifier as $notifier) {
            if ($use_cache) if ($this->cache->get($id)) {
                $this->log(self::LOG_DEBUG, sprintf("%s: Cache hit: %s", get_class($notifier), $id));
                continue;
            }

            $notifier->notify($value);
            $this->cache->set($id, $value);
        }
    }

    public function log($level, $message)
    {
        if ($this->log_level & $level) {
            printf("[%s][%s] %s\n", getmypid(), date('Y-m-d H:i:s'), $message);
        }
    }
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    protected function getCache($config)
    {
        if (is_null($driver = @$config['driver'] ?: null)) {
            throw new \RuntimeException("Cache config parameter 'driver' must be set.");
        }
        if ($this->hasCacheDriver($driver)) {
            $driver_name = $this->getCacheDriverName($driver);
            return new $driver_name($config['options']);
        }

        throw new \RuntimeException("Cache '$driver' is not exists");
    }

    protected function hasCacheDriver($name)
    {
        return class_exists($this->getCacheDriverName($name));
    }

    protected function getCacheDriverName($name)
    {
        return __NAMESPACE__ . '\\Cache\\' . $name;
    }

    protected function getNotifier($name, $options)
    {
        if ($this->hasNotifier($name)) {
            $notifier = $this->getNotifierName($name);
            return new $notifier($options);
        }

        throw new \RuntimeException("Notifier '$name' is not exists");
    }

    protected function hasNotifier($name)
    {
        return class_exists($this->getNotifierName($name));
    }

    protected function getNotifierName($name)
    {
        return __NAMESPACE__ . '\\Notifier\\' . $name;
    }
}
