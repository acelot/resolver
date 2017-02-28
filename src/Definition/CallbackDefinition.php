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
     * Creates the definition with given callback function.
     *
     * @param callable $callback Callback function
     *
     * @return CallbackDefinition
     */
    public static function define(callable $callback): CallbackDefinition
    {
        return new CallbackDefinition($callback);
    }

    /**
     * @param callable $callback Callback function
     */
    private function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Returns the callback function.
     *
     * @return callable Callback function
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Resolves and invoke the callback function.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
    {
        $ref = new \ReflectionFunction($this->getCallback());
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

            throw new ResolverException(sprintf('Cannot resolve the callback "%s"', $ref->getName()));
        }

        return $ref->invokeArgs($args);
    }
}
