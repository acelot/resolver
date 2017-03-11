<?php declare(strict_types = 1);

namespace Acelot\Resolver\Tests\Functional\Fixtures;

class Repository implements RepositoryInterface
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function find(int $id): Entity
    {
        $data = $this->db->find($id);

        $entity = new Entity();
        $entity->setId($data['id']);
        $entity->setText($data['text']);

        return $entity;
    }

    public function save(Entity $entity): Entity
    {
        if (!$entity->getId()) {
            $id = $this->db->create([
                'id' => $entity->getId(),
                'text' => $entity->getText()
            ]);

            $entity->setId($id);
        } else {
            $this->db->update($entity->getId(), [
                'text' => $entity->getText()
            ]);
        }

        return $entity;
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }

    public function getDb(): Database
    {
        return $this->db;
    }
}