<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ClassDefinition;
use Acelot\Resolver\Definition\ClosureDefinition;
use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Functional\Fixtures\Config;
use Acelot\Resolver\Tests\Functional\Fixtures\ConfigFactory;
use PHPUnit\Framework\TestCase;

class DefinitionsTest extends TestCase
{
    public function testObjectDefinition()
    {
        $config = new Config([]);

        $resolver = new Resolver();
        $resolver->bind(Config::class, ObjectDefinition::define($config));

        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertSame($config, $resolvedConfig);
    }

    public function testClosureDefinition()
    {
        $resolver = new Resolver();
        $resolver->bind(Config::class, ClosureDefinition::define(function () {
            return new Config([]);
        }));

        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testClassDefinition()
    {
        $resolver = new Resolver();
        $resolver->bind(
            Config::class,
            ClassDefinition::define(Config::class)->withArgument('config', ['test' => 'ok'])
        );

        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertArrayHasKey('test', $resolvedConfig);
    }

    public function testFactoryDefinition()
    {
        $resolver = new Resolver();
        $resolver->bind(
            Config::class,
            FactoryDefinition::define(ConfigFactory::class, 'create')
        );

        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertArrayHasKey('test', $resolvedConfig);
    }
}