<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;

trait IfExistsTrait
{

    public function dropTablesIfExists(Schema $schema, array $tables)
    {
        foreach ($tables as $table) {
            $this->dropOneTableIfExists($schema, $table);
        }
    }

    public function dropOneTableIfExists(Schema $schema, string $table): void
    {
        if ($schema->hasTable($table)) {
            $this->addSql("drop table {$table}");
        }
    }

    public function addSqlIfTableDoesNotExists(Schema $schema, string $table, string $sql): void
    {
        if (!$schema->hasTable($table)) {
            $this->addSql($sql);
        }
    }
}