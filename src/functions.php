<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Definition\ValueDefinition;

/**
 * @param string $fqcn
 * @param string $method
 *
 * @return FactoryDefinition
 */
function factory(callable $callable): FactoryDefinition
{
    return FactoryDefinition::define($callable);
}

/**
 * @param string $fqcn
 *
 * @return ValueDefinition
 */
function object(string $fqcn): ObjectDefinition
{
    return ObjectDefinition::define($fqcn);
}

/**
 * @param object $value
 *
 * @return ValueDefinition
 */
function value($value): ValueDefinition
{
    return ValueDefinition::define($value);
}