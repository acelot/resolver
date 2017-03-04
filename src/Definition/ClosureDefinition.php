<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

class ClosureDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * Creates the definition with given closure function.
     *
     * @param \Closure $closure Closure function
     *
     * @return ClosureDefinition
     */
    public static function define(\Closure $closure): ClosureDefinition
    {
        return new ClosureDefinition($closure);
    }

    /**
     * @param \Closure $closure Closure function
     */
    private function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Resolves and invoke the closure function.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
    {
        $ref = new \ReflectionFunction($this->closure);
        $args = $this->resolveParameters($ref->getParameters(), $resolver);

        return call_user_func($this->closure, ...$args);
    }
}
