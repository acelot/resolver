<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

class CallbackDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $callback;

    /**
     * @param callable $callback
     * @return static
     */
    public static function define(callable $callback): CallbackDefinition
    {
        return new CallbackDefinition($callback);
    }

    /**
     * @param callable $callback
     */
    private function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @param ResolverInterface $resolver
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
    {
        $ref = new \ReflectionFunction($this->getCallback());
        $args = [];

        foreach ($ref->getParameters() as $param) {
            if ($this->hasArg($param->getName())) {
                $args[] = $this->getArg($param->getName());
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

            throw new ResolverException(
                sprintf('Cannot resolve the callback "%s"', $ref->getName())
            );
        }

        return $ref->invokeArgs($args);
    }
}
