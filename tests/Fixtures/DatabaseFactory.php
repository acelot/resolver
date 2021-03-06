<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

class DatabaseFactory
{
    public static function create(Config $config)
    {
        return new Database($config['db.host'], $config['db.port']);
    }
}
