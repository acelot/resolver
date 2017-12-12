<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Fixtures;

class Database
{
    protected $host;

    protected $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function find($id)
    {
        return [
            'id' => $id,
            'text' => 'Some text'
        ];
    }

    public function create($data): int
    {
        return 1;
    }

    public function update($id, $data)
    {
        return true;
    }

    public function delete($id)
    {
        return true;
    }
}
