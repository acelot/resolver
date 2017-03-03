<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Meta\FunctionMeta;
use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

class ClassDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var string
     */
    protected $fqcn;

    /**
     * Creates the definition with given class name.
     *
     * @param string $fqcn Fully qualified class name
     *
     * @return ClassDefinition
     */
    public static function define(string $fqcn): ClassDefinition
    {
        return new ClassDefinition($fqcn);
    }

    /**
     * @param string $fqcn Fully qualified class name
     */
    private function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
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

        $key = self::class . ':' . $this->fqcn;

        $fromCache = $cache->get($key);
        if ($fromCache === null) {
            try {
                $factoryMethod = new \ReflectionMethod($this->fqcn, '__construct');
                $functionMeta = FunctionMeta::fromReflection($factoryMethod);
            } catch (\ReflectionException $e) {
                $functionMeta = new FunctionMeta([]);
            }

            $cache->set($key, serialize($functionMeta), 24 * 60 * 60);
        } else {
            $functionMeta = unserialize($fromCache);
        }

        $args = iterator_to_array($this->resolveParameters($functionMeta, $resolver));

        return new $this->fqcn(...$args);
    }
}