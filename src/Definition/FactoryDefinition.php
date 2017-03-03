<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Meta\FunctionMeta;
use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

class FactoryDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $fqcn;

    /**
     * @var null|string
     */
    protected $method;

    /**
     * Creates the definition with given class name and factory method.
     *
     * @param string $fqcn   Fully qualified class name
     * @param string $method Factory method
     *
     * @return FactoryDefinition
     */
    public static function define(string $fqcn, string $method = '__invoke'): FactoryDefinition
    {
        return new FactoryDefinition($fqcn, $method);
    }

    /**
     * @param string $fqcn   Fully qualified class name
     * @param string $method Factory method
     */
    private function __construct(string $fqcn, string $method)
    {
        $this->fqcn = $fqcn;
        $this->method = $method;
    }

    /**
     * Resolves and returns the instance of the class.
     *
     * @param ResolverInterface $resolver
     * @param CacheInterface    $cache
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache)
    {
        if (!class_exists($this->fqcn)) {
            throw new ResolverException(sprintf('The class "%s" does not exists', $this->fqcn));
        }

        $key = self::class . ':' . $this->fqcn . ':' . $this->method;

        $fromCache = $cache->get($key);
        if ($fromCache === null) {
            try {
                $factoryMethod = new \ReflectionMethod($this->fqcn, $this->method);
            } catch (\ReflectionException $e) {
                throw new ResolverException(sprintf(
                    'The factory method "%s" does not exists in the class "%s"',
                    $this->method,
                    $this->fqcn
                ));
            }

            if (!$factoryMethod->isStatic()) {
                throw new ResolverException(sprintf('The factory method "%s" must be static', $this->method));
            }

            $functionMeta = FunctionMeta::fromReflection($factoryMethod);

            $cache->set($key, serialize($functionMeta), 24 * 60 * 60);
        } else {
            $functionMeta = unserialize($fromCache);
        }

        $args = iterator_to_array($this->resolveParameters($functionMeta, $resolver));

        return call_user_func_array([$this->fqcn, $this->method], $args);
    }
}