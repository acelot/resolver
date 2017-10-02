<?php declare(strict_types=1);

namespace Acelot\Resolver;

interface DefinitionInterface
{
    /**
     * Is definition result must be shared between calls.
     *
     * @return bool
     */
    public function isShared(): bool;

    /**
     * Resolves the definition.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     */
    public function resolve(ResolverInterface $resolver);
}