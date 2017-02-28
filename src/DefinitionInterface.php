<?php declare(strict_types = 1);

namespace Acelot\Resolver;

interface DefinitionInterface
{
    /**
     * @param ResolverInterface $resolver
     * @return object
     */
    public function resolve(ResolverInterface $resolver);
}