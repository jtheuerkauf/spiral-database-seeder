<?php

declare(strict_types=1);

namespace Spiral\DatabaseSeeder\Database\TestStrategy;

use Spiral\DatabaseSeeder\Database\DatabaseState;
use Spiral\DatabaseSeeder\Database\Exception\DatabaseMigrationsException;
use Spiral\Testing\TestCase;

/**
 * Use the `createMigration` parameter set to `false` if you want to use production application migrations.
 * No new migrations will be created and no migrations will be deleted.
 *
 * Use the `createMigration` parameter set to `true` if you want to use test application migrations.
 * Migrations will be created before the test is executed and deleted after execution.
 */
class MigrationStrategy
{
    /**
     * @param TestCase $testCase
     * @param bool $createMigrations
     */
    public function __construct(
        protected TestCase $testCase,
        protected bool $createMigrations = false
    ) {
    }

    public function migrate(): void
    {
        $this->createMigrations
            ? $this->testCase->runCommand('cycle:migrate', ['--run' => true])
            : $this->testCase->runCommand('migrate');

        DatabaseState::$migrated = true;
    }

    public function rollback(): void
    {
        $this->testCase->runCommand('migrate:rollback', ['--all' => true]);

        if ($this->createMigrations) {
            $this->testCase->cleanupDirectories($this->getMigrationsDirectory());
        }

        DatabaseState::$migrated = false;
    }

    public function disableCreationMigrations(): void
    {
        $this->createMigrations = false;
    }

    public function enableCreationMigrations(): void
    {
        $this->createMigrations = true;
    }

    protected function getMigrationsDirectory(): string
    {
        $config = $this->testCase->getConfig('migration');
        if (empty($config['directory'])) {
            throw new DatabaseMigrationsException(
                'Please, configure migrations in your test application to use DatabaseMigrations.'
            );
        }

        if (!isset($config['safe']) || $config['safe'] !== true) {
            throw new DatabaseMigrationsException(
                'The `safe` parameter in the test application migrations configuration must be set to true.'
            );
        }

        return $config['directory'];
    }
}
