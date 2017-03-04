<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Acelot\Resolver\Cache\NullCache;
use Acelot\Resolver\Definition\ClassDefinition;
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
     * @var array
     */
    protected $definitions;

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param array $definitions Definitions mapping
     */
    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $fqcn => $definition) {
            $this->bind($fqcn, $definition);
        }

        $this->resolved[ResolverInterface::class] = $this;

        $this->cache = new NullCache();
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
            $definition = ClassDefinition::define($fqcn);
        }

        $this->resolved[$fqcn] = $definition->resolve($this, $this->cache);

        return $this->resolved[$fqcn];
    }
}
