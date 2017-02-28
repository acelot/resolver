<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

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
     * @param CacheInterface    $cache
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache)
    {
        $ref = new \ReflectionFunction($this->closure);
        $args = [];

        foreach ($ref->getParameters() as $param) {
            if ($this->hasArgument($param->getName())) {
                $args[] = $this->getArgument($param->getName());
                continue;
            }

            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }

            $paramClass = $param->getClass();
            if ($paramClass !== null) {
                $args[] = $resolver->resolve($paramClass->getName());
                continue;
            }

            throw new ResolverException(sprintf('Cannot resolve the closure function "%s"', $ref->getName()));
        }

        return $ref->invokeArgs($args);
    }
}
