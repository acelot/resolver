<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

interface RepositoryInterface
{
    public function find(int $id): Entity;

    public function save(Entity $entity): Entity;

    public function delete(int $id): void;
}
