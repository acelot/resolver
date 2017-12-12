<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ValueDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Fixtures\Config;

use PHPUnit\Framework\TestCase;

class ValueDefinitionTest extends TestCase
{
    public function testResolve()
    {
        $config = new Config([]);

        $resolver = new Resolver([
            Config::class => ValueDefinition::define($config)
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
        $this->assertSame($config, $resolvedConfig);
    }
}
