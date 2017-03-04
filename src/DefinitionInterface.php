<?php declare(strict_types = 1);

namespace Acelot\Resolver;

interface DefinitionInterface
{
    /**
     * Resolves the definition.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     */
    public function resolve(ResolverInterface $resolver);
}