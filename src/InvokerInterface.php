<?php declare(strict_types = 1);

namespace Acelot\Resolver;

interface InvokerInterface
{
    /**
     * Invoke a callable.
     *
     * @param callable $callable Callable
     *
     * @return mixed
     */
    public function invoke(callable $callable);
}