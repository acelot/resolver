<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Definition\ObjectDefinition;

/**
 * @example
 *
 *   $resolver = new Resolver([
 *       LoggerInterface::class => ClassDefinition::define(LoggerFactory::class),
 *       Database::class => ClassDefinition::define(MongoDbFactory::class)
 *   ]);
 *
 */
class Resolver implements ResolverInterface, InvokerInterface
{
    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @param array $definitions Definitions mapping
     */
    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $fqcn => $definition) {
            $this->bind($fqcn, $definition);
        }

        $this->resolved[ResolverInterface::class] = $this;
        $this->resolved[InvokerInterface::class] = $this;
    }

    /**
     * Binds the class name to definition.
     *
     * @param string              $fqcn       Fully qualified class name
     * @param DefinitionInterface $definition Definition
     *
     * @return $this
     */
    public function bind(string $fqcn, DefinitionInterface $definition)
    {
        $this->definitions[$fqcn] = $definition;
        return $this;
    }

    /**
     * Resolves and returns the instance of the class.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return object
     */
    public function resolve(string $fqcn)
    {
        if (array_key_exists($fqcn, $this->resolved)) {
            return $this->resolved[$fqcn];
        }

        if (array_key_exists($fqcn, $this->definitions)) {
            $definition = $this->definitions[$fqcn];
        } else {
            $definition = ObjectDefinition::define($fqcn);
        }

        $this->resolved[$fqcn] = $definition->resolve($this);

        return $this->resolved[$fqcn];
    }

    /**
     * Invoke a callable.
     *
     * @param callable $callable Callable
     * @param array    $args     Arguments
     *
     * @return mixed
     */
    public function invoke(callable $callable, array $args = [])
    {
        return FactoryDefinition::define($callable)
            ->withArguments($args)
            ->resolve($this);
    }
}
