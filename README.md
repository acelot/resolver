# Resolver

[![Build Status](https://travis-ci.org/acelot/resolver.svg?branch=master)](https://travis-ci.org/acelot/resolver)
[![Code Climate](https://img.shields.io/codeclimate/coverage/acelot/resolver.svg)](https://codeclimate.com/github/acelot/resolver)
![](https://img.shields.io/badge/dependencies-zero-blue.svg)
![](https://img.shields.io/badge/license-MIT-green.svg)

**Resolver** is a dependency auto resolver for PHP 7. Supports PSR-11 `ContainerInterface`.

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
Now imagine that you have ten or hundred controllers like this?!
With **Resolver** you can greatly simplify creation of classes. 
In what turns this code using **Resolver**:

```php
$resolver = new Resolver([
    Database::class => ObjectDefinition::define(Database::class)->withArgument('connectionString', 'connection string here')
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

- FactoryDefinition (short alias `factory()`)
- ObjectDefinition (short alias `object()`)
- ValueDefinition (short alias `value()`)

### Instance sharing

Resolved definitions can be shared between calls via `->shared()` method. This method available in `FactoryDefinition` and `ObjectDefinition`. `ValueDefinition` is shared always by design.

---

**Resolver** (c) by Valeriy Protopopov.

**Resolver** is licensed under a MIT license.
