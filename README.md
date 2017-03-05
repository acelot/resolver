# Resolver

[![Build Status](https://travis-ci.org/acelot/resolver.svg?branch=master)](https://travis-ci.org/acelot/resolver)

**Resolver** is a dependency auto resolver for PHP 7.

### Installation

```
composer require acelot/resolver
```

### Why?

Imagine that you have a controller:

```php
class UsersController
{
    public function __construct(UsersService $service)
    {
        // ...
    }
}
```

As you can see the controller requires `UsersService` in constructor. To resolve this dependency you can just pass
the new instance of `UsersService`. Let's do this:

```php
$service = new UsersService();
$controller = new UsersController($service);
```

It doesn't work, because `UsersService`, in turn, requires `UsersRepository` to access the data.

```php
class UsersService
{
    public function __construct(UsersRepository $repository)
    {
        // ...
    }
}
```

Okay, let's create the repository instance!

```php
$repository = new UsersRepository();
$service = new UsersService($repository);
$controller = new UsersController($service);
```

Sadly, it still doesn't work, because we encountering the new dependency! The repository, surprisingly, requires 
a database connection :)

```php
class UsersRepository
{
    public function __construct(Database $db)
    {
        // ...
    }
}
```

You say "Eat this!".

```php
$db = new Database('connection string here');
$repository = new UsersRepository($db);
$service = new UsersService($repository);
$controller = new UsersController($service);
```

Success! We have finally created the instance of `UsersController`!

**Too. Many. Words. For. This. Simple. Shit!**

In what turns this code using **Resolver**:

```php
$resolver = new Resolver([
    Database::class => ClassDefinition::define(Database::class)->withArgument('connectionString', 'connection string here')
]);

$controller = $resolver->resolve(UsersController::class);
```

And it's all.


### How it works?

**Resolver** resolves the classes by using [Reflection](http://php.net/manual/ru/book.reflection.php).
Through reflection the **Resolver** finds out all dependencies of the class and all dependencies of 
dependencies and so on. When **Resolver** reaches the deepest dependency it starts creating instances 
of these one by one until the top class. The resolved classes are stored in local array to avoid re-resolving.

### Available definitions

- ClosureDefinition
- FactoryDefinition
- ClassDefinition
- ObjectDefinition

### Detailed example

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