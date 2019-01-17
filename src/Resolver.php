<?php declare(strict_types=1);

namespace Acelot\Resolver;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Exception\DefinitionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * @example
 *
 *   $resolver = new Resolver([
 *       LoggerInterface::class => ClassDefinition::define(LoggerFactory::class),
 *       Database::class => ClassDefinition::define(MongoDbFactory::class)
 *   ]);
 *
 */
class Resolver implements ResolverInterface, InvokerInterface, ContainerInterface
{
    /**
     * @var array[string]DefinitionInterface
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $shared = [];

    /**
     * @param array[string]DefinitionInterface $definitions Definitions mapping
     *
     * @return Resolver
     */
    public static function create(array $definitions = []): Resolver
    {
        return new static($definitions);
    }

    /**
     * @param array[string]DefinitionInterface $definitions Definitions mapping
     */
    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $fqcn => $definition) {
            if (!$definition instanceof DefinitionInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Definition of "%s" must implement DefinitionInterface', $fqcn)
                );
            }
        }

        $this->definitions = $definitions;
        $this->shared[ContainerInterface::class] = $this;
        $this->shared[ResolverInterface::class] = $this;
        $this->shared[InvokerInterface::class] = $this;
    }

    /**
     * Returns all bound definitions.
     *
     * @return array[string]DefinitionInterface
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * Binds the class name to definition. Immutable.
     *
     * @param string              $fqcn Fully qualified class name
     * @param DefinitionInterface $definition Definition
     *
     * @return Resolver
     */
    public function withDefinition(string $fqcn, DefinitionInterface $definition): Resolver
    {
        $clone = clone $this;
        $clone->definitions[$fqcn] = $definition;
        return $clone;
    }

    /**
     * Unbinds the definition by class name. Immutable.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return Resolver
     */
    public function withoutDefinition(string $fqcn): Resolver
    {
        $clone = clone $this;
        unset($clone->definitions[$fqcn]);
        return $clone;
    }

    /**
     * Resolves and returns the instance of the class.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return object
     * @throws DefinitionException
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
     * @param array    $args Arguments
     *
     * @return mixed
     * @throws Exception\ResolverException
     */
    public function invoke(callable $callable, array $args = [])
    {
        return FactoryDefinition::define($callable)
            ->withArguments($args)
            ->resolve($this);
    }

    /**
     * @param string $id
     *
     * @return mixed|object
     * @throws ContainerExceptionInterface
     */
    public function get($id)
    {
        return $this->resolve($id);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return true;
    }

    /**
     * Removes the resolved object from shared items.
     * This is useful when you want the object to be re-resolved.
     *
     * @param string $fqcn
     */
    public function unshare(string $fqcn): void
    {
        unset($this->shared[$fqcn]);
    }

    /**
     * Clear all the resolved objects from shared items.
     * @see `removeShared` method
     */
    public function unshareAll(): void
    {
        $this->shared = [];
    }
}
