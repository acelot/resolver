<?php declare(strict_types=1);

namespace Acelot\Resolver;

use Acelot\Resolver\Exception\DefinitionException;

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
     * @throws DefinitionException
     */
    public function resolve(ResolverInterface $resolver);
}
