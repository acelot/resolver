<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Psr\SimpleCache\CacheInterface;
use Acelot\Resolver\Definition\ClassDefinition;

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
     * @param CacheInterface $cache
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
        $this->resolved[ResolverInterface::class] = $this;
    }

    /**
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @param string $fqcn
     * @param DefinitionInterface $definition
     * @return $this
     */
    public function bind(string $fqcn, DefinitionInterface $definition)
    {
        $this->definitions[$fqcn] = $definition;
        return $this;
    }

    /**
     * @param string $fqcn
     * @return object
     */
    public function resolve(string $fqcn)
    {
        if (array_key_exists($fqcn, $this->definitions)) {
            $definition = $this->definitions[$fqcn];
        } else {
            $definition = ClassDefinition::define($fqcn);
        }

        if (!array_key_exists($fqcn, $this->resolved)) {
            $this->resolved[$fqcn] = $definition->resolve($this);
        }

        return $this->resolved[$fqcn];
    }
}
