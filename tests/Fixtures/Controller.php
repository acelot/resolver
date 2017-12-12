<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

class Controller
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getSomething()
    {
        $entity = $this->service->get(1);

        return [
            'id' => $entity->getId(),
            'text' => $entity->getText()
        ];
    }
}
