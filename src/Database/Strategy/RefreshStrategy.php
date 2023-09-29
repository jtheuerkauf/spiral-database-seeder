<?php

declare(strict_types=1);

namespace Spiral\DatabaseSeeder\Database\TestStrategy;

use Spiral\DatabaseSeeder\Database\Cleaner;

class RefreshStrategy
{
    public function __construct(
        protected Cleaner $cleaner,
        protected bool $useAttribute = false,
        protected ?string $database = null,
        protected array $except = []
    ) {
    }

    public function refresh(): void
    {
        $this->cleaner->refreshDb(database: $this->database, except: $this->except);
    }

    public function enableRefreshAttribute(): void
    {
        $this->useAttribute = true;
    }

    public function disableRefreshAttribute(): void
    {
        $this->useAttribute = false;
    }

    public function isRefreshAttributeEnabled(): bool
    {
        return $this->useAttribute;
    }

    public function setDatabase(?string $database = null): void
    {
        $this->database = $database;
    }

    public function setExcept(array $except = []): void
    {
        $this->except = $except;
    }
}
