<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition\Meta;

class FunctionMeta
{
    /**
     * @var ParameterMeta[]
     */
    protected $parameters;

    /**
     * @param \ReflectionFunctionAbstract $ref
     *
     * @return FunctionMeta
     */
    public static function fromReflection(\ReflectionFunctionAbstract $ref): FunctionMeta
    {
        return new FunctionMeta(array_map(function (\ReflectionParameter $param) {
            return ParameterMeta::fromReflection($param);
        }, $ref->getParameters()));
    }

    /**
     * @param ParameterMeta[] $parameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return ParameterMeta[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}