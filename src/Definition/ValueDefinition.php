<?php declare(strict_types=1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\ResolverInterface;

class ValueDefinition implements DefinitionInterface
{
    /**
     * @var object
     */
    protected $value;

    /**
     * Creates the definition with given value.
     *
     * @param object|mixed $value Object value
     *
     * @return ValueDefinition
     * @throws \InvalidArgumentException
     */
    public static function define($value): ValueDefinition
    {
        return new ValueDefinition($value);
    }

    /**
     * @param object|mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException('Value must be an object');
        }

        $this->value = $value;
    }

    /**
     * Is definition result must be shared between calls.
     *
     * @return bool
     */
    public function isShared(): bool
    {
        return true;
    }

    /**
     * Resolves the definition. Simply returns the value.
     *
     * @param ResolverInterface $resolver
     *
     * @return object
     */
    public function resolve(ResolverInterface $resolver)
    {
        return $this->value;
    }
}
