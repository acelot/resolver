<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Functional\Fixtures\Config;

use PHPUnit\Framework\TestCase;

class ObjectDefinitionTest extends TestCase
{
    public function testObjectDefinition()
    {
        $resolver = new Resolver([
            Config::class => ObjectDefinition::define(Config::class)->withArgument('config', ['test' => 'ok'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
        $this->assertArrayHasKey('test', $resolvedConfig);
    }
}