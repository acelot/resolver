<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

class Service
{
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get($id): Entity
    {
        return $this->repository->find($id);
    }

    public function updateText($id, $text): void
    {
        $entity = $this->repository->find($id);
        $entity->setText($text);

        $this->repository->save($entity);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
