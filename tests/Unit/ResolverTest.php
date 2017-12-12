<?php declare(strict_types=1);

namespace Acelot\Resolver\Unit;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Fixtures\Config;
use Acelot\Resolver\Tests\Fixtures\ConfigFactory;
use Acelot\Resolver\Tests\Fixtures\Database;
use Acelot\Resolver\Tests\Fixtures\DatabaseFactory;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public function testShouldCreateResolverWithoutDefinitions()
    {
        $resolver = new Resolver();
        $this->assertEmpty($resolver->getDefinitions());
    }

    public function testShouldCreateResolverWithDefinitions()
    {
        $definitions = [
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create']),
        ];

        $resolver = new Resolver($definitions);
        $this->assertEquals($definitions, $resolver->getDefinitions());
    }

    public function testShouldBindDefinition()
    {
        $definitions = [
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create']),
        ];

        $resolver = new Resolver();
        foreach ($definitions as $fqcn => $definition) {
            $resolver = $resolver->withDefinition($fqcn, $definition);
        }

        $this->assertEquals($definitions, $resolver->getDefinitions());
    }

    public function testShouldUnbindDefinition()
    {
        $definitions = [
            Config::class => FactoryDefinition::define([ConfigFactory::class, 'create']),
            Database::class => FactoryDefinition::define([DatabaseFactory::class, 'create']),
        ];

        $resolver = Resolver::create($definitions)->withoutDefinition(Database::class);
        $this->assertArrayNotHasKey(Database::class, $resolver->getDefinitions());
    }
}
