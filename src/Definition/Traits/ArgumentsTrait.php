<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition\Traits;

trait ArgumentsTrait
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * @param string $name
     * @return bool
     */
    public function hasArg(string $name): bool
    {
        return array_key_exists($name, $this->args);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function getArg(string $name)
    {
        if (!$this->hasArg($name)) {
            throw new \OutOfBoundsException();
        }

        return $this->args[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withArg(string $name, $value)
    {
        $clone = clone $this;
        $clone->args[$name] = $value;

        return $clone;
    }
}