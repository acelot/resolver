<?php declare(strict_types = 1);

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
     * @param object $value
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function define($value)
    {
        return new static($value);
    }

    /**
     * @param object $value
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
     * @return object
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param ResolverInterface $resolver
     * @return object
     */
    public function resolve(ResolverInterface $resolver)
    {
        return $this->getValue();
    }
}