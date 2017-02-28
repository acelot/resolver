<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Psr\SimpleCache\CacheInterface;

interface DefinitionInterface
{
    /**
     * Resolves the definition.
     *
     * @param ResolverInterface $resolver
     * @return object
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache);
}