<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Psr\SimpleCache\CacheInterface;

/**
 * @example
 *
 *   $resolver = new Resolver([
 *       LoggerInterface::class => ClassDefinition::define(MonologLoggerFactory::class),
 *       Database::class => ClassDefinition::define(MongoDbFactory::class)
 *   ]);
 *
 */
class Resolver implements ResolverInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @param array $definitions Definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
        $this->resolved[ResolverInterface::class] = $this;
    }

    /**
     * Sets the cache provider.
     *
     * @param CacheInterface $cache
     *
     * @return $this
     */
    public function useCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Binds the definition after constructor.
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
        if (array_key_exists($fqcn, $this->definitions)) {
            $definition = $this->definitions[$fqcn];
        } else {
            $definition = Definition\ClassDefinition::define($fqcn);
        }

        if (!array_key_exists($fqcn, $this->resolved)) {
            $this->resolved[$fqcn] = $definition->resolve($this, $this->cache);
        }

        return $this->resolved[$fqcn];
    }
}
