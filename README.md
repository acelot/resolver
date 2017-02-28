# Resolver

**Resolver** is a dependency auto resolver for PHP 7.

### Installation

```
composer require acelot/resolver
```

### How it works?

**Resolver** resolves the classes by using [Reflection](http://php.net/manual/ru/book.reflection.php). Through reflection the **Resolver** finds out all dependencies of the class and all dependencies of dependencies and so on. When **Resolver** reaches the deepest dependency it starts creating instances of these one by one until the top class. The resolved classes are stored in local array to avoid re-resolving.

### But Reflection is too slow for production?

**Resolver** can use any [PSR-16](http://www.php-fig.org/psr/psr-16/) compatible cache provider through `useCache(CacheInterface $cache)` method. For example, [matthiasmullie/scrapbook](https://github.com/matthiasmullie/scrapbook):

```php
$client = new \Memcached();
$client->addServer('localhost', 11211);
$cache = new \MatthiasMullie\Scrapbook\Adapters\Memcached($client);

$resolver = new Resolver();
$resolver->useCache($cache);
```

### Available definitions

- ClosureDefinition
- FactoryDefinition
- ClassDefinition
- ValueDefinition

### Example

**Logger Factory** `LoggerFactory.php`

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

**Database Factory** `MongoDbFactory.php`

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

**Repository** `Respository.php`

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
    
    public function get(int $id): array
    {
        try {
            return $this->db->findOne(['_id' => $id])->toArray();
        } catch (MongoDbException $e) {
            $this->logger->error('Error occurred while fetching the document!');
            throw new RepositoryException();
        }
    }

    public function create(array $data): array
    {
        // ...
    }

    public function update(int $id, array $data): array
    {
        // ...
    }

    public function delete(int $id): void
    {
        // ...
    }
}
```

**App** `index.php`

```php
namespace Acme;

use MongoDB\Database;
use Psr\Log\LoggerInterface;

$definitions = [
    Config::class =>
        ClosureDefinition::define(function () {
            return new Config([
                'logger.channel' => 'acme_channel',
                'mongodb.uri' => 'mongodb://localhost/mydb'
            ]);
        }),

    LoggerInterface::class => 
        FactoryDefinition::define(LoggerFactory::class, 'create'),
        
    Database::class =>
        FactoryDefinition::define(MongoDbFactory::class, 'create')
];

$resolver = new Resolver($definitions);

/** @var Repository $repository */
$repository = $resolver->resolve(Repository::class);
echo json_encode($respository->get(1));
```