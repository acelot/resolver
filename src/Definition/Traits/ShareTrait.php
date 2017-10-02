<?php declare(strict_types=1);

namespace Acelot\Resolver\Definition\Traits;

trait ShareTrait
{
    /**
     * @var bool
     */
    protected $isShared = false;

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->isShared;
    }

    /**
     * @param bool $shared
     *
     * @return static
     */
    public function shared(bool $shared = true)
    {
        $clone = clone $this;
        $clone->isShared = $shared;

        return $clone;
    }
}