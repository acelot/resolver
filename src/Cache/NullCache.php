<?php declare(strict_types = 1);

namespace Acelot\Resolver\Cache;

use Psr\SimpleCache\CacheInterface;

/**
 * Null cache provider.
 * For development purposes.
 */
class NullCache implements CacheInterface
{
    /**
     * @param string $key
     * @param null   $default
     *
     * @return null
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * @param string                 $key
     * @param mixed                  $value
     * @param null|int|\DateInterval $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return true;
    }

    /**
     * @param iterable   $keys
     * @param null|mixed $default
     *
     * @return \Generator
     */
    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as $key) {
            yield $default;
        }
    }

    /**
     * @param iterable               $values
     * @param null|int|\DateInterval $ttl
     *
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return true;
    }

    /**
     * @param iterable $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return false;
    }
}