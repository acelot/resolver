<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Psr\SimpleCache\CacheInterface;

interface DefinitionInterface
{
    /**
     * Resolves the definition.
     *
     * @param ResolverInterface   $resolver
     * @param CacheInterface $cache
     *
     * @return object
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache);
}