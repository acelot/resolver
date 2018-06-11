<?php declare(strict_types = 1);

namespace Acelot\Resolver\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * The general exception thrown by Resolver.
 */
class ResolverException extends \Exception implements ContainerExceptionInterface
{
}
