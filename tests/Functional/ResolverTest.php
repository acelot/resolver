<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional;

use Acelot\Resolver\Definition\ObjectDefinition;
use Acelot\Resolver\Definition\FactoryDefinition;
use Acelot\Resolver\Resolver;
use Acelot\Resolver\Tests\Functional\Fixtures\Config;
use Acelot\Resolver\Tests\Functional\Fixtures\ConfigFactory;
use Acelot\Resolver\Tests\Functional\Fixtures\Database;
use Acelot\Resolver\Tests\Functional\Fixtures\DatabaseFactory;
use Acelot\Resolver\Tests\Functional\Fixtures\Repository;
use Acelot\Resolver\Tests\Functional\Fixtures\RepositoryInterface;
use Acelot\Resolver\Tests\Functional\Fixtures\Service;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public function testResolveClassWithNestedDependencies()
    {
        $resolver = new Resolver();
        $resolver->bind(Config::class, FactoryDefinition::define([ConfigFactory::class, 'create']));
        $resolver->bind(Database::class, FactoryDefinition::define([DatabaseFactory::class, 'create']));
        $resolver->bind(RepositoryInterface::class, ObjectDefinition::define(Repository::class));

        /** @var Service $resolvedService */
        $resolvedService = $resolver->resolve(Service::class);
        self::assertInstanceOf(Service::class, $resolvedService);

        /** @var Repository $resolvedRepository */
        $resolvedRepository = $resolver->resolve(RepositoryInterface::class);
        self::assertSame($resolvedService->getRepository(), $resolvedRepository);

        /** @var Database $resolvedDatabase */
        $resolvedDatabase = $resolver->resolve(Database::class);
        self::assertSame($resolvedRepository->getDb(), $resolvedDatabase);
    }
}