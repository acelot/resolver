<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

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
     * @param string $class
     * @return static
     */
    public static function define(string $fqcn): ClassDefinition
    {
        return new ClassDefinition($fqcn);
    }

    /**
     * @param string $class
     */
    private function __construct(string $class)
    {
        $this->fqcn = $class;
    }

    /**
     * @return string
     */
    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    /**
     * @return null|string
     */
    public function getFactoryMethod(): ?string
    {
        return $this->factoryMethod;
    }

    /**
     * @param null|string $name
     * @param mixed $value
     * @return ClassDefinition
     */
    public function withFactoryMethod(?string $method): ClassDefinition
    {
        $clone = clone $this;
        $clone->factoryMethod = $method;

        return $clone;
    }

    /**
     * @param ResolverInterface $resolver
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
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
                sprintf('Cannot resolve the class "%s"', $ref->getName())
            );
        }

        if ($factoryMethod) {
            return call_user_func_array([$this->getFqcn(), $factoryMethod], $args);
        }

        return $ref->newInstanceArgs($args);
    }
}