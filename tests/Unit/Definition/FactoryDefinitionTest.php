<?php declare(strict_types=1);

namespace Acelot\Resolver\Unit;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Tests\Fixtures\ConfigFactory;
use PHPUnit\Framework\TestCase;

class FactoryDefinitionTest extends TestCase
{
    public function testShouldCreateDefinition()
    {
        $callable = [ConfigFactory::class, 'create'];
        $definition = new FactoryDefinition($callable);
        $this->assertEquals($callable, $definition->getCallable());
    }

    public function testShouldCreateDefinitionWithArgument()
    {
        $definition = FactoryDefinition::define([ConfigFactory::class, 'create'])->withArgument('foo', 'bar');
        $this->assertEquals('bar', $definition->getArgument('foo'));
    }

    public function testShouldCreateDefinitionWithArguments()
    {
        $args = [
            'foo' => 'bar',
            'boo' => 'far'
        ];
        $definition = FactoryDefinition::define([ConfigFactory::class, 'create'])->withArguments($args);
        $this->assertEquals($args['foo'], $definition->getArgument('foo'));
        $this->assertEquals($args['boo'], $definition->getArgument('boo'));
    }

    public function testShouldCreateDefinitionWithoutArgument()
    {
        $args = [
            'foo' => 'bar',
            'boo' => 'far'
        ];
        $definition = FactoryDefinition::define([ConfigFactory::class, 'create'])
            ->withArguments($args)
            ->withoutArgument('foo');
        $this->assertEquals($args['boo'], $definition->getArgument('boo'));
        $this->expectException(\OutOfBoundsException::class);
        $definition->getArgument('foo');
    }
}
