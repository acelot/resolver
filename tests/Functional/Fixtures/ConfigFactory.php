<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional\Fixtures;

class ConfigFactory
{
    public static function create()
    {
        return new Config([
            'db.host' => 'localhost',
            'db.port' => 10000
        ]);
    }

    public function __invoke()
    {
        return self::create();
    }
}