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
    protected $shared = [];

    /**
     * @param array $definitions Definitions mapping
     */
    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $fqcn => $definition) {
            $this->bind($fqcn, $definition);
        }

        $this->shared[ResolverInterface::class] = $this;
        $this->shared[InvokerInterface::class] = $this;
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
        // Search class in shared
        if (array_key_exists($fqcn, $this->shared)) {
            return $this->shared[$fqcn];
        }

        // Search definition in predefined definition, otherwise use ObjectDefinition
        if (array_key_exists($fqcn, $this->definitions)) {
            $definition = $this->definitions[$fqcn];
        } else {
            $definition = ObjectDefinition::define($fqcn);
        }

        // Resolving
        $result = $definition->resolve($this);

        // Sharing result between calls
        if ($definition->isShared()) {
            $this->shared[$fqcn] = $result;
        }

        return $result;
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
