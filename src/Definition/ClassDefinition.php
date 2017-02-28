<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

class ClassDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $fqcn;

    /**
     * @var null|string
     */
    protected $factoryMethod;

    /**
     * Creates the definition with given class name.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return ClassDefinition
     */
    public static function define(string $fqcn): ClassDefinition
    {
        return new ClassDefinition($fqcn);
    }

    /**
     * @param string $fqcn Fully qualified class name
     */
    private function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    /**
     * Returns the fully qualified class name.
     *
     * @return string
     */
    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    /**
     * Returns the factory method name.
     *
     * @return null|string
     */
    public function getFactoryMethod(): ?string
    {
        return $this->factoryMethod;
    }

    /**
     * Returns the new instance of the definition with new factory method.
     *
     * @param null|string $method Factory method name
     *
     * @return ClassDefinition
     */
    public function withFactoryMethod(?string $method): ClassDefinition
    {
        $clone = clone $this;
        $clone->factoryMethod = $method;

        return $clone;
    }

    /**
     * Resolves and returns the instance of the class.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache)
    {
        $ref = new \ReflectionClass($this->getFqcn());
        $factoryMethod = $this->getFactoryMethod();

        if ($factoryMethod) {
            $factory = $ref->getMethod($factoryMethod);
        } else {
            $factory = $ref->getConstructor();
            if ($factory === null) {
                return $ref->newInstance();
            }
        }

        $args = [];

        foreach ($factory->getParameters() as $param) {
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

            throw new ResolverException(
                sprintf('Cannot resolve the class "%s"', $ref->getName())
            );
        }

        if ($factoryMethod) {
            return call_user_func_array([$this->getFqcn(), $factoryMethod], $args);
        }

        return $ref->newInstanceArgs($args);
    }
}