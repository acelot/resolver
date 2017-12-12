<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

class Config implements \ArrayAccess
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }
}
