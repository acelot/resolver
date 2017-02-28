<?php declare(strict_types = 1);

namespace Acelot\Resolver;

interface ResolverInterface
{
    /**
     * @param string $fqcn
     * @return mixed
     */
    public function resolve(string $fqcn);
}