<?php

namespace App\Migrations;

trait ForeignKeyChecksTrait
{
    public function setForeignKeyChecks($check = null): void
    {
        $value = $check ? 1 : 0;
        $this->addSql("SET FOREIGN_KEY_CHECKS={$value};");
    }
}