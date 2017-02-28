<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition\Traits;

trait ArgumentsTrait
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * Checks whether the argument exists.
     *
     * @param string $name Argument name
     *
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        return array_key_exists($name, $this->args);
    }

    /**
     * Returns the argument value.
     *
     * @param string $name Argument name
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function getArgument(string $name)
    {
        if (!$this->hasArgument($name)) {
            throw new \OutOfBoundsException(sprintf('The argument "%s" is not exists', $name));
        }

        return $this->args[$name];
    }

    /**
     * Returns the new instance of the trait holding class with new argument.
     *
     * @param string $name  Argument name
     * @param mixed  $value Argument value
     *
     * @return static
     */
    public function withArgument(string $name, $value)
    {
        $clone = clone $this;
        $clone->args[$name] = $value;

        return $clone;
    }
}