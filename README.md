# Resolver

Resolver is a dependency auto resolver for PHP 7.

## Usage

1. Create factories

`LoggerFactory.php`

```php
namespace Acme;

use Psr\Log\LoggerInterface;

class LoggerFactory
{
    /**
     * @param Config $config
     * @return LoggerInterface
     */
    public static function create(Config $config): LoggerInterface
    {
        return new MySuperLogger($config->get('logger.channel'));
    }
}
```

`MongoDbFactory.php`

```php
namespace Acme;

use MongoDB\Database;

class LoggerFactory
{
    /**
     * @param Config $config
     * @return Database
     */
    public static function create(Config $config): Database
    {
        return new Database($config->get('mongodb.uri'));
    }
}
```

2. Some repositories

`Respository.php`

```php
namespace Acme;

use MongoDB\Database;
use MongoDB\Driver\Exception\Exception as MongoDbException;
use Psr\Log\LoggerInterface;

class Repository implements SomeRepositoryInterface
{
    /**
     * @var Database
     */
    protected $db;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
        
    /**
     * @param Database $db
     * @param LoggerInterface $logger
     */
    public function __contruct(Database $db, LoggerInterface $logger)
    {
        return new Database($config->get('mongodb.uri'));
    }
    
    /**
     * @param int $id
     * @return array
     * @throws RepositoryException
     */
    public function get(int $id): array
    {
        try {
            return $this->db->findOne(['_id' => $id])->toArray();
        } catch (MongoDbException $e) {
            $this->logger->error('Error occurred while fetching the document!');
            throw new RepositoryException();
        }
    }
    
    /**
     * @param array $data
     * @return array
     * @throws RepositoryException
     */
    public function create(array $data): array
    {
        // ...
    }
    
    /**
     * @param int $id
     * @return array $data
     * @throws RepositoryException
     */
    public function update(int $id, array $data): array
    {
        $this->logger->debug('Updating document...');
        
        try {
            return $this->db->updateOne(['_id' => $id], $data)->toArray();
        } catch (MongoDbException $e) {
            $this->logger->error('Error occurred while updating the document!');
            throw new RepositoryException();
        }
    }
    
    /**
     * @param int $id
     * @throws RepositoryException
     */
    public function delete(int $id): void
    {
        // ...
    }
}
```

3. Resolve dependecies automatically

`index.php`

```php
namespace Acme;

use MongoDB\Database;
use Psr\Log\LoggerInterface;

$definitions = [
    Config::class =>
        CallbackDefinition::define(function () {
            return new Config([
                'logger.channel' => 'acme_channel',
                'mongodb.uri' => 'mongodb://localhost/mydb'
            ]);
        }),

    LoggerInterface::class => 
        ClassDefinition::define(LoggerFactory::class)->withFactoryMethod('create'),
        
    Database::class =>
        ClassDefinition::define(MongoDbFactory::class)->withFactoryMethod('create')
];

$resolver = new Resolver($definitions);

/** @var Repository $repository */
$repository = $resolver->resolve(Repository::class);
echo json_encode($respository->get(1));
```