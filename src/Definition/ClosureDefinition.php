<?php declare(strict_types = 1);

namespace Acelot\Resolver\Definition;

use Acelot\Resolver\Definition\Meta\FunctionMeta;
use Acelot\Resolver\Definition\Traits\ArgumentsTrait;
use Acelot\Resolver\DefinitionInterface;
use Acelot\Resolver\Exception\ResolverException;
use Acelot\Resolver\ResolverInterface;
use Psr\SimpleCache\CacheInterface;

class ClosureDefinition implements DefinitionInterface
{
    use ArgumentsTrait;

    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * Creates the definition with given closure function.
     *
     * @param \Closure $closure Closure function
     *
     * @return ClosureDefinition
     */
    public static function define(\Closure $closure): ClosureDefinition
    {
        return new ClosureDefinition($closure);
    }

    /**
     * @param \Closure $closure Closure function
     */
    private function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Resolves and invoke the closure function.
     *
     * @param ResolverInterface $resolver
     * @param CacheInterface    $cache
     *
     * @return object
     * @throws ResolverException
     */
    public function resolve(ResolverInterface $resolver, CacheInterface $cache)
    {
        $key = self::class . ':' . md5(serialize($this->closure));

        $fromCache = $cache->get($key);
        if ($fromCache === null) {
            $ref = new \ReflectionFunction($this->closure);
            $functionMeta = FunctionMeta::fromReflection($ref);
            $cache->set($key, serialize($functionMeta), 24 * 60 * 60);
        } else {
            $functionMeta = unserialize($fromCache);
        }

        $args = iterator_to_array($this->resolveParameters($functionMeta, $resolver));

        return call_user_func_array($this->closure, $args);
    }
}
