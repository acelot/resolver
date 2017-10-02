<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional\Fixtures;

class ValueHolder
{
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}