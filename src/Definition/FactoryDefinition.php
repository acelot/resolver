<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

class FactoryDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $fqcn;

    /**
     * @var null|string
     */
    protected $method;

    /**
     * Creates the definition with given class name and factory method.
     *
     * @param string $fqcn   Fully qualified class name
     * @param string $method Factory method
     *
     * @return FactoryDefinition
     */
    public static function define(string $fqcn, string $method = '__invoke'): FactoryDefinition
    {
        return new FactoryDefinition($fqcn, $method);
    }

    /**
     * @param string $fqcn   Fully qualified class name
     * @param string $method Factory method
     */
    private function __construct(string $fqcn, string $method)
    {
        $this->fqcn = $fqcn;
        $this->method = $method;
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
     * Resolves and returns the instance of the class.
     *
     * @param ResolverInterface $resolver
     * @param CacheInterface    $cache
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache)
    {
        try {
            $ref = new \ReflectionClass($this->getFqcn());
        } catch (\ReflectionException $e) {
            throw new ResolverException(sprintf('The class "%s" does not exists', $this->getFqcn()));
        }

        try {
            $factoryMethod = $ref->getMethod($this->method);
        } catch (\ReflectionException $e) {
            throw new ResolverException(sprintf(
                'The factory method "%s" does not exists in the class',
                $this->method
            ));
        }

        $args = [];

        foreach ($factoryMethod->getParameters() as $param) {
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

        return call_user_func_array([$this->getFqcn(), $factoryMethod], $args);
    }
}