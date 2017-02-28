<?php declare(strict_types = 1);

namespace Acelot\Resolver;

interface ResolverInterface
{
    /**
     * Resolves class by given class name.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return mixed
     */
    public function resolve(string $fqcn);
}