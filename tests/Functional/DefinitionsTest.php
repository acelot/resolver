<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Definition\ValueDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Functional\Fixtures\Config;
use Acelot\Resolver\Tests\Functional\Fixtures\ConfigFactory;

use PHPUnit\Framework\TestCase;

class DefinitionsTest extends TestCase
{
    public function testValueDefinition()
    {
        $config = new Config([]);

        $resolver = new Resolver([
            Config::class => ValueDefinition::define($config)
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertSame($config, $resolvedConfig);
    }

    public function testCallableDefinitionWithClosure()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define(function () {
                return new Config([]);
            })
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testCallableDefinitionWithArray1()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testCallableDefinitionWithArray2()
    {
        $factory = new ConfigFactory();

        $resolver = new Resolver([
            Config::class => FactoryDefinition::define([$factory, 'create'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testCallableDefinitionWithObject()
    {
        $factory = new ConfigFactory();

        $resolver = new Resolver([
            Config::class => FactoryDefinition::define($factory)
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testCallableDefinitionWithString1()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define(
                'Acelot\Resolver\Tests\Functional\Fixtures\ConfigFactory::create'
            )
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testObjectDefinition()
    {
        $resolver = new Resolver([
            Config::class => ObjectDefinition::define(Config::class)->withArgument('config', ['test' => 'ok'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertArrayHasKey('test', $resolvedConfig);
    }

    public function testFactoryDefinition()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        self::assertInstanceOf(Config::class, $resolvedConfig);
        self::assertArrayHasKey('db.host', $resolvedConfig);
    }
}