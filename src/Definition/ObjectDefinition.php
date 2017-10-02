<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\Definition\Traits\ShareTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

class ObjectDefinition implements DefinitionInterface
{
    use ShareTrait;
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $fqcn;

    /**
     * Creates the definition with given class name.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return ObjectDefinition
     */
    public static function define(string $fqcn): ObjectDefinition
    {
        return new ObjectDefinition($fqcn);
    }

    /**
     * @param string $fqcn Fully qualified class name
     */
    public function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    /**
     * Resolves and returns the instance of the class.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver)
    {
        if (!class_exists($this->fqcn)) {
            throw new ResolverException(sprintf('The class "%s" does not exists', $this->fqcn));
        }

        try {
            $ref = new \ReflectionMethod($this->fqcn, '__construct');
        } catch (\ReflectionException $e) {
            return new $this->fqcn();
        }

        $args = $this->resolveParameters($ref->getParameters(), $resolver);

        return new $this->fqcn(...$args);
    }
}