<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition\Traits;

use Acelot\Resolver\Definition\Meta\FunctionMeta;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;

trait ArgumentsTrait
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * Checks whether the argument exists.
     *
     * @param string $name Argument name
     *
     * @return bool
     */
    public function hasArgument(string $name): bool
    {
        return array_key_exists($name, $this->args);
    }

    /**
     * Returns the argument value.
     *
     * @param string $name Argument name
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function getArgument(string $name)
    {
        if (!$this->hasArgument($name)) {
            throw new \OutOfBoundsException(sprintf('The argument "%s" is not exists', $name));
        }

        return $this->args[$name];
    }

    /**
     * Returns the new instance of the trait holding class with new argument.
     *
     * @param string $name  Argument name
     * @param mixed  $value Argument value
     *
     * @return static
     */
    public function withArgument(string $name, $value)
    {
        $clone = clone $this;
        $clone->args[$name] = $value;

        return $clone;
    }

    /**
     * Resolves function parameters of the given function meta information.
     *
     * @param FunctionMeta      $parameters
     * @param ResolverInterface $resolver
     *
     * @return \Iterator
     */
    protected function resolveParameters(FunctionMeta $functionMeta, ResolverInterface $resolver): \Iterator
    {
        foreach ($functionMeta->getParameters() as $param) {
            if ($this->hasArgument($param->getName())) {
                yield $this->getArgument($param->getName());
                continue;
            }

            if ($param->hasDefaultValue()) {
                yield $param->getDefaultValue();
                continue;
            }

            $paramClass = $param->getClassName();
            if ($paramClass !== null) {
                yield $resolver->resolve($paramClass);
                continue;
            }

            throw new ResolverException(sprintf(
                'Cannot resolve the function because parameter "%s" require unknown value',
                $this->fqcn,
                $param->getName()
            ));
        }
    }
}