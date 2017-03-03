<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Tests\Functional\Fixtures\Config;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public function shouldResolveObjectDefinitions()
    {
        $resolver = new Resolver();
        $resolver->bind(Config::class, ObjectDefinition::define(new Config([])));

        /** @var Config $config */
        $config = $resolver->resolve(Config::class);
    }
}