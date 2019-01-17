<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Resolver;

use Acelot\Resolver\Tests\Fixtures\ValueHolder;
use PHPUnit\Framework\TestCase;

class ShareTest extends TestCase
{
    public function testShared()
    {
        $resolver = new Resolver([
            ValueHolder::class => FactoryDefinition::define(function () {
                return new ValueHolder(microtime());
            })->shared()
        ]);

        /** @var ValueHolder $resolvedRepository */
        $resolved1 = $resolver->resolve(ValueHolder::class);
        usleep(100);
        $resolved2 = $resolver->resolve(ValueHolder::class);

        $this->assertSame($resolved1, $resolved2);
    }

    public function testNotShared()
    {
        $resolver = new Resolver([
            ValueHolder::class => FactoryDefinition::define(function () {
                return new ValueHolder(microtime());
            })->shared(false)
        ]);

        /** @var ValueHolder $resolvedRepository */
        $resolved1 = $resolver->resolve(ValueHolder::class);
        usleep(100);
        $resolved2 = $resolver->resolve(ValueHolder::class);

        $this->assertNotSame($resolved1, $resolved2);
    }

    public function testRemoveShared()
    {
        $resolver = new Resolver([
            ValueHolder::class => FactoryDefinition::define(function () {
                return new ValueHolder(microtime());
            })->shared()
        ]);

        /** @var ValueHolder $resolvedRepository */
        $resolved1 = $resolver->resolve(ValueHolder::class);
        $resolver->unshare(ValueHolder::class);
        usleep(100);
        $resolved2 = $resolver->resolve(ValueHolder::class);

        $this->assertNotSame($resolved1, $resolved2);
    }
}
