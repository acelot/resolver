<?php declare(strict_types = 1);

namespace Acelot\Resolver;

use Acelot\Resolver\Definition\{
    FactoryDefinition, ObjectDefinition, ValueDefinition
};

/**
 * @param callable $callable
 *
 * @return FactoryDefinition
 */
function factory(callable $callable): FactoryDefinition
{
    return new FactoryDefinition($callable);
}

/**
 * @param string $fqcn
 *
 * @return ObjectDefinition
 */
function object(string $fqcn): ObjectDefinition
{
    return new ObjectDefinition($fqcn);
}

/**
 * @param object $value
 *
 * @return ValueDefinition
 */
function value($value): ValueDefinition
{
    return new ValueDefinition($value);
}