<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

class FactoryDefinition implements DefinitionInterface
{
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
    }

    /**
     * Resolves and invoke the callable.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
    {
        $type = $this->getCallableType();

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
                    throw new ResolverException('Use ObjectDefinition instead of FactoryDefinition');
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
                    throw new ResolverException('Use ObjectDefinition instead of FactoryDefinition');
                }

                $ref = new \ReflectionMethod($fqcn, $method);
                $args = $this->resolveParameters($ref->getParameters(), $resolver);

                return call_user_func($this->callable, ...$args);

            default:
                throw new ResolverException('Unknown callable type');
        }
    }

    /**
     * @param callable $callable
     *
     * @return int
     */
    protected function getCallableType(): int
    {
        // Closure
        if ($this->callable instanceof \Closure) {
            return self::TYPE_CLOSURE;
        }

        // Object
        if (is_object($this->callable)) {
            return self::TYPE_OBJECT;
        }

        // Array
        if (is_array($this->callable)) {
            if (is_object($this->callable[0])) {
                return self::TYPE_ARRAY_OBJECT;
            }

            return self::TYPE_ARRAY;
        }

        // String
        if (is_string($this->callable)) {
            if (strpos($this->callable, '::') !== false) {
                return self::TYPE_STRING_SEPARATED;
            }

            return self::TYPE_STRING;
        }

        return self::TYPE_UNKNOWN;
    }
}
