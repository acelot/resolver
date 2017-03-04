<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\ResolverInterface;

class ObjectDefinition implements DefinitionInterface
{
    /**
     * @var object
     */
    protected $value;

    /**
     * Creates the definition with given value.
     *
     * @param object $value Object value
     *
     * @return ObjectDefinition
     * @throws \InvalidArgumentException
     */
    public static function define($value): ObjectDefinition
    {
        return new ObjectDefinition($value);
    }

    /**
     * @param object $value
     *
     * @throws \InvalidArgumentException
     */
    private function __construct($value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException('Value must be an object');
        }

        $this->value = $value;
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