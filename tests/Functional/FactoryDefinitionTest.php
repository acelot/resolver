<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Fixtures\Config;
use Acelot\Resolver\Tests\Fixtures\ConfigFactory;

use PHPUnit\Framework\TestCase;

class FactoryDefinitionTest extends TestCase
{
    public function testClosure()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define(function () {
                return new Config([]);
            })
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testArray()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testArrayWithObject()
    {
        $factory = new ConfigFactory();

        $resolver = new Resolver([
            Config::class => FactoryDefinition::define([$factory, 'create'])
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testObject()
    {
        $factory = new ConfigFactory();

        $resolver = new Resolver([
            Config::class => FactoryDefinition::define($factory)
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
    }

    public function testSeparatedString()
    {
        $resolver = new Resolver([
            Config::class => FactoryDefinition::define(
                ConfigFactory::class . '::create'
            )
        ]);

        /** @var Config $resolvedRepository */
        $resolvedConfig = $resolver->resolve(Config::class);

        $this->assertInstanceOf(Config::class, $resolvedConfig);
    }
}
