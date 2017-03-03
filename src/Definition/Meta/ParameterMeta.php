<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition\Meta;

class ParameterMeta
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var bool
     */
    protected $hasDefaultValue;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * \ReflectionParameter $param
     */
    public static function fromReflection(\ReflectionParameter $param): ParameterMeta
    {
        return new ParameterMeta(
            $param->getName(),
            $param->getClass() ? $param->getClass()->getName() : null,
            $param->isDefaultValueAvailable(),
            $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
        );
    }

    /**
     * @param $name
     * @param $className
     * @param $hasDefaultValue
     * @param $defaultValue
     */
    public function __construct($name, $className, $hasDefaultValue, $defaultValue)
    {
        $this->name = $name;
        $this->className = $className;
        $this->hasDefaultValue = $hasDefaultValue;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}