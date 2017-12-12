<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\Definition\Traits\ShareTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\DefinitionException;
use Acelot\Resolver\ResolverInterface;

class FactoryDefinition implements DefinitionInterface
{
    use ShareTrait;
    use ArgumentsTrait;

    protected const TYPE_UNKNOWN = -1;
    protected const TYPE_CLOSURE = 0;
    protected const TYPE_OBJECT = 1;
    protected const TYPE_ARRAY = 2;
    protected const TYPE_ARRAY_OBJECT = 3;
    protected const TYPE_STRING = 4;
    protected const TYPE_STRING_SEPARATED = 5;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * Creates the definition with given callable.
     *
     * @param callable $callable Callable
     *
     * @return FactoryDefinition
     */
    public static function define(callable $callable): FactoryDefinition
    {
        return new FactoryDefinition($callable);
    }

    /**
     * @param callable $callable Callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->isShared = true;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }

    /**
     * @param callable $callable
     *
     * @return FactoryDefinition
     */
    public function withCallable(callable $callable): FactoryDefinition
    {
        $clone = clone $this;
        $clone->callable = $callable;

        return $clone;
    }

    /**
     * Resolves and invoke the callable.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws DefinitionException
     */
    public function resolve(ResolverInterface $resolver)
    {
        $type = self::getCallableType($this->callable);

        switch ($type) {
            case self::TYPE_CLOSURE:
            case self::TYPE_STRING:
                $ref = new \ReflectionFunction($this->callable);
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                return call_user_func($this->callable, ...$args);

            case self::TYPE_OBJECT:
                $ref = new \ReflectionMethod($this->callable, '__invoke');
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                return call_user_func($this->callable, ...$args);

            case self::TYPE_ARRAY_OBJECT:
                $ref = new \ReflectionMethod($this->callable[0], $this->callable[1]);
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                return $ref->invoke($this->callable[0], ...$args);

            case self::TYPE_ARRAY:
                list($fqcn, $method) = $this->callable;
                if ($method === '__construct') {
                    throw new DefinitionException('Use ObjectDefinition instead of FactoryDefinition');
                }

                $ref = new \ReflectionMethod($fqcn, $method);
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                if ($ref->isStatic()) {
                    return call_user_func($this->callable, ...$args);
                }

                return $ref->invoke($resolver->resolve($fqcn), ...$args);

            case self::TYPE_STRING_SEPARATED:
                list($fqcn, $method) = explode('::', $this->callable);
                if ($method === '__construct') {
                    throw new DefinitionException('Use ObjectDefinition instead of FactoryDefinition');
                }

                $ref = new \ReflectionMethod($fqcn, $method);
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                return call_user_func($this->callable, ...$args);

            default:
                throw new DefinitionException('Unknown callable type');
        }
    }

    /**
     * Returns the type of callable.
     *
     * @param callable $callable
     *
     * @return int
     */
    protected static function getCallableType(callable $callable): int
    {
        // Closure
        if ($callable instanceof \Closure) {
            return self::TYPE_CLOSURE;
        }

        // Object
        if (is_object($callable)) {
            return self::TYPE_OBJECT;
        }

        // Array
        if (is_array($callable)) {
            if (is_object($callable[0])) {
                return self::TYPE_ARRAY_OBJECT;
            }

            return self::TYPE_ARRAY;
        }

        // String
        if (is_string($callable)) {
            if (strpos($callable, '::') !== false) {
                return self::TYPE_STRING_SEPARATED;
            }

            return self::TYPE_STRING;
        }

        return self::TYPE_UNKNOWN;
    }
}
